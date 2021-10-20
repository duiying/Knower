@extends('layouts.app')
@section('header')
    <li class="breadcrumb-item active">菜单</li>
@endsection
@section('content')
    <script src="/storage/js/validate/menu.validate.js"></script>
    <script src="/storage/js/form/menu.form.js"></script>
    <div class="card">
        <div class="card-header">
            <form id="user-search" onsubmit="return false;">
                <div class="row">
                    <div class="input-group-append mr-1">
                        <a href="/view/menu/create"><button type="button" class="btn btn-block btn-outline-primary"><i class="fas fa-plus"></i></button></a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                <tr>
                    <th style="width: 50px">ID</th>
                    <th style="width: 50px">排序</th>
                    <th style="width: 50px">标题</th>
                    <th style="width: 50px">图标</th>
                    <th style="width: 150px">路由</th>
                    <th style="width: 150px">操作</th>
                </tr>
                </thead>
                <tbody id="menu-list">
                </tbody>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
    <script type="text/javascript">
        /**
         * 渲染菜单列表
         */
        function renderMenuList()
        {
            var data = searchMenu();

            if (data !== false) {
                menuList = data.list;

                var listHtml = '';

                for (var i = 0; i < menuList.length; i++) {
                    listHtml += '<tr>';
                    listHtml += '<td>' + menuList[i].id + '</td>';
                    listHtml += '<td>' + menuList[i].sort + '</td>';
                    listHtml += '<td class="menu-down" isFold="0" value="' + menuList[i].id + '"><i class="fas fa-folder-minus mr-1 menu-folder-icon' + menuList[i].id + '"></i>' + menuList[i].name + '</td>';
                    listHtml += '<td>' + '<i class="mr-2 ' + menuList[i].icon + '"></i>' + menuList[i].icon + '</td>';
                    listHtml += '<td>' + menuList[i].url + '</td>';
                    listHtml += '<td>';
                    listHtml += '<a href="/view/menu/update?id=' + menuList[i].id + '"><i class="fas fa-edit"></i></a>';
                    listHtml += '<a href="javascript:;" class="ml-2" onclick="handleMenuDelete(' + menuList[i].id + ')"><i class="fas fa-trash"></i></a>';
                    listHtml += '</td>';
                    listHtml += '</tr>';

                    for (var j = 0; j < menuList[i].sub_menu_list.length; j++) {
                        listHtml += '<tr class="sub-menu-' + menuList[i].id + '">';
                        listHtml += '<td>' + menuList[i].sub_menu_list[j].id + '</td>';
                        listHtml += '<td>' + menuList[i].sub_menu_list[j].sort + '</td>';
                        listHtml += '<td><span class="ml-4">' + menuList[i].sub_menu_list[j].name + '</span></td>';
                        listHtml += '<td>' + '<i class="mr-2 ' + menuList[i].sub_menu_list[j].icon + '"></i>' + menuList[i].sub_menu_list[j].icon + '</td>';
                        listHtml += '<td><code>' + menuList[i].sub_menu_list[j].url + '</code></td>';
                        listHtml += '<td>';
                        listHtml += '<a href="/view/menu/update?id=' + menuList[i].sub_menu_list[j].id + '"><i class="fas fa-edit"></i></a>';
                        listHtml += '<a href="javascript:;" class="ml-2" onclick="handleMenuDelete(' + menuList[i].sub_menu_list[j].id + ')"><i class="fas fa-trash"></i></a>';
                        listHtml += '</td>';
                        listHtml += '</tr>';
                    }
                }

                $('#menu-list').html(listHtml);
            }
        }

        renderMenuList();

        // 折叠效果
        $('.menu-down').css('cursor', 'pointer');
        $('.menu-down').click(function () {
            var pid = $(this).attr('value');

            if ($(this).attr('isFold') == "1") {
                $('.sub-menu-' + pid).removeClass('none');
                $(this).attr('isFold', "0");
                $('.menu-folder-icon' + pid).removeClass('fa-folder-plus');
                $('.menu-folder-icon' + pid).addClass('fa-folder-minus');
            } else {
                $('.sub-menu-' + pid).addClass('none');
                $(this).attr('isFold', "1");
                $('.menu-folder-icon' + pid).removeClass('fa-folder-minus');
                $('.menu-folder-icon' + pid).addClass('fa-folder-plus');
            }
        });

        function handleMenuDelete(id)
        {
            handleDeleteCallback(function () {
                var param   = {id : id, status : -1}
                var data    = updateMenuField(param)
                if (data !== false) {
                    renderMenuList();
                }
            });
        }
    </script>
@endsection

