<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/" class="brand-link">
        <img src="/storage/AdminLTE/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Admin</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false" id="left-menu-list">

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

<script>
    // 渲染菜单数据
    var menuList = getUserMenu();

    if (menuList !== false) {
        menuList = menuList.list;

        var menuListHtml = '';

        for (var i = 0; i < menuList.length; i++) {
            if (menuList[i].url === '') menuList[i].url = '#';

            menuListHtml += '<li class="nav-item">';
            menuListHtml += '<a href="' + menuList[i].url + '" class="nav-link">';
            menuListHtml += '<p class="col-2">';
            menuListHtml += '<i class="' + menuList[i].icon + '"></i>';
            menuListHtml += '<p>' + menuList[i].name;
            if (menuList[i].sub_menu_list.length > 0) menuListHtml += '<i class="right fas fa-angle-left"></i>';
            menuListHtml += '</p>';
            menuListHtml += '</p>';
            menuListHtml += '</a>';
            menuListHtml += '<ul class="nav nav-treeview">';

            for (var j = 0; j < menuList[i].sub_menu_list.length; j++) {
                menuListHtml += '<li class="nav-item">';
                menuListHtml += '<a href="' + menuList[i].sub_menu_list[j].url + '" class="nav-link">';
                menuListHtml += '<p class="col-2">';
                menuListHtml += '<i style="float: left;" class=" ' + menuList[i].sub_menu_list[j].icon + '"></i>';
                menuListHtml += '<p>' + menuList[i].sub_menu_list[j].name + '</p>';
                menuListHtml += '</p>';
                menuListHtml += '</a>';
                menuListHtml += '</li>';
            }

            menuListHtml += '</ul>';
            menuListHtml += '</li>';
        }

        $('#left-menu-list').html(menuListHtml);
    }
</script>