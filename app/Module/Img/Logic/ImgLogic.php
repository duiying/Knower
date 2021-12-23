<?php

namespace App\Module\Img\Logic;

use App\Constant\AppErrorCode;
use App\Module\Img\Constant\ImgConstant;
use App\Util\AppException;
use App\Util\Log;
use Hyperf\Di\Annotation\Inject;
use App\Module\Img\Service\ImgService;
use Hyperf\Utils\Coroutine;

class ImgLogic
{
    /**
     * @Inject()
     * @var ImgService
     */
    private $service;

    /**
     * 检查图片 ID 是否存在
     *
     * @param $id
     */
    public function checkImgExist($id)
    {
        $img = $this->service->getLineByWhere(['id' => $id, 'status' => ImgConstant::IMG_STATUS_NORMAL], ['id']);
        if (empty($img)) {
            throw new AppException(AppErrorCode::PARAMS_INVALID);
        }
    }

    /**
     * 根据远程图片 url 获取本地图片 url
     *
     * @param array $originUrlList
     * @return array
     */
    public function getImgLocalUrlByOriginUrl($originUrlList = [])
    {
        $originUrlList = array_unique($originUrlList);
        $map = [];
        if (empty($originUrlList)) {
            return $map;
        }
        $imgInfoList = $this->service->getImgLocalUrlByOriginUrl($originUrlList);
        foreach ($imgInfoList as $k => $v) {
            if (!empty($v['local_url'])) {
                $map[$v['origin_url']] = $v['local_url'];
            }
        }

        return $map;
    }

    /**
     * 根据图片原 url，返回图片 id
     *
     * @param string $originUrl
     * @return int
     */
    public function findOrCreateImgByOriginUrl($originUrl = '')
    {
        if (empty($originUrl)) return 0;

        // 1、先检查该图片 url 是否已在表中，如果存在，直接返回图片 id
        $imgInfo = $this->service->getLineByWhere(['origin_url' => $originUrl, 'status' => ImgConstant::IMG_STATUS_NORMAL]);

        if (!empty($imgInfo)) {
            return intval($imgInfo['id']);
        }

        // 2、如果图片 url 不在表中，新建一条记录
        $createImgParams = [
            'origin_url'    => $originUrl,
            'local_url'     => '',
        ];

        return $this->service->create($createImgParams);
    }

    /**
     * 根据图片 id 获取图片 url
     *
     * @param array $imgIdList
     * @return array
     */
    public function getImgUrlMapByIdList($imgIdList = [])
    {
        if (empty($imgIdList)) return [];

        $imgIdList = array_filter(array_unique($imgIdList));

        $imgInfoList = $this->service->search(['id' => $imgIdList, 'status' => ImgConstant::IMG_STATUS_NORMAL]);

        $imgUrlMap = [];

        foreach ($imgInfoList as $k => $v) {
            $imgUrlMap[$v['id']] = !empty($v['local_url']) ? $v['local_url'] : $v['origin_url'];
        }

        return $imgUrlMap;
    }

    /**
     * 创建
     *
     * @param $originUrl
     * @param string $localUrl
     * @return int
     */
    public function create($originUrl, $localUrl = '')
    {
        $createImgParams = [
            'origin_url'    => $originUrl,
            'local_url'     => $localUrl
        ];

        return $this->service->create($createImgParams);
    }

    /**
     * 生成新的文件名前缀
     *
     * @return string
     */
    public function genNewPreFileName()
    {
        return date('YmdHis') . rand(1000, 9999);
    }

    /**
     * 图片保存目录
     *
     * @return string
     */
    public function getSaveDir()
    {
        $dir = BASE_PATH . '/public';
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        return $dir;
    }

    /**
     * 根据远程图片 url 下载图片到本地（这里使用了协程并发处理）
     *
     * @param $url
     */
    public function download($url)
    {
        Coroutine::create(function () use ($url) {
            // 1、先检查图片是否已下载过
            $imgInfo = $this->service->getLineByWhere(['origin_url' => $url, 'status' => ImgConstant::IMG_STATUS_NORMAL], ['id']);
            if (!empty($imgInfo)) {
                return false;
            }

            Log::info('开始下载图片', ['url' => $url]);

            // 2、根据图片 url 解析出后缀名、文件名
            $pathInfo = pathinfo($url);
            $ext = $pathInfo['extension'];
            $filename = $pathInfo['basename'];
            if (!in_array($ext, ImgConstant::ALLOWED_EXT)) {
                return false;
            }

            // 中文的 url 无法解析，在这里处理一下
            $encodedUrl = $this->parseUrl($url);

            // 3、下载图片到本地
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
            curl_setopt($ch, CURLOPT_URL, $encodedUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            $headers = [];
            $headers[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.93 Safari/537.36';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $file = curl_exec($ch);
            $httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                Log::info('下载文件失败', ['code' => $httpCode, 'url' => $url]);
                return false;
            }

            // 如果文件名已存在，则重新命名
            if (file_exists($this->getSaveDir() . '/' . $filename)) {
                $filename = $this->genNewPreFileName() . '.' . $ext;
            }

            // 写入本地
            $resource = fopen($this->getSaveDir() . '/' . $filename, 'a');
            fwrite($resource, $file);
            fclose($resource);

            // 4、插入一条记录
            $this->service->create(['origin_url' => $url, 'local_url' => '/public/' . $filename]);

            Log::info('图片下载完成', ['url' => $url]);

            return true;
        });
    }

    /**
     * 解决 url 中带中文的问题
     *
     * @param $url
     * @return string|string[]
     */
    public function parseUrl($url)
    {
        $url = rawurlencode($url);
        $a = ["%3A", "%2F", "%40"];
        $b = [":", "/", "@"];
        $url = str_replace($a, $b, $url);
        return $url;
    }
}