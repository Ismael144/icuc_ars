<div class="app-menu navbar-menu" id="sidebar-menu" style="overflow-y: auto;">
    <div class="icuc-sidebar position-relative" style="overflow-y: auto;">
        <!-- LOGO -->
        <div class="navbar-brand-box py-1 mt-1">
            <div class="d-flex align-items-center gap-2 my-2" style="background: #192626; padding: 10px; border-radius: 20px;">
                <img src="/icuc_ars/app/assets/images/logo.png" alt="" style="width: 60px;">
                <div class="desc mb-1 d-flex flex-column" style="align-items:flex-start; ">
                    <h5 class="text-white mb-1" style="font-weight: 300 !important;">ICUC</h5>
                    <small class="text-white">ARM System</small>
                </div>

            </div>
            <button type="button" class="btn btn-sm p-0 fs-3xl header-item float-end btn-vertical-sm-hover" id="vertical-hover">
                <i class="ri-record-circle-line"></i>
            </button>
        </div>

        <div id="scrollbar" style="height: 100%;">
            <div class="container-fluid">
                <div id="two-column-menu">
                </div>
                <header class="menu-title mx-1" style="list-style: none;"><span data-key="t-menu">Menu</span></header>
                <ul class="navbar-nav mx-4 py-2" id="navbar-nav" style="background: #192626; border-radius: 20px;">
                    <li class="nav-item">
                        <a class="nav-link menu-link collapsed" href="/icuc_ars/app/dashboard">
                            <i class="ph-gauge"></i> <span data-key="t-dashboards">Dashboard</span>
                        </a>
                    </li>
                    <?php if (isAdmin()) : ?>
                        <li class="nav-item">
                            <a href="/icuc_ars/app/users/index" class="nav-link menu-link"> <i class="ph-user"></i> <span data-key="t-calendar">Users</span> </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <?php
                        // For the link 
                        $link = isAdmin() ? "/icuc_ars/app/staff_data/index" : "/icuc_ars/app/staff_data/images/index?id=" . $authUser['id'];
                        ?>
                        <a href="<?= $link ?>" class="nav-link menu-link"> <i class="ph-chats"></i> <span data-key="t-chat">
                                <?php if (!isAdmin()) : ?>
                                    Your Data
                                <?php else : ?>
                                    Staff Data
                                <?php endif; ?>
                            </span></a>
                    </li>

                    <li class="nav-item">
                        <a href="/icuc_ars/app/attendance/index " class="nav-link menu-link"> <i class="ph-align-left"></i> <span data-key="t-attendance">Attendance</span> </a>
                    </li>
                    <li class="nav-item">
                        <a href="/icuc_ars/app/attendance/holidays/index " class="nav-link "> <i class="ph-calendar"></i> <span data-key="t-holidays">Holidays</span> </a>
                    </li>
                    <li class="nav-item">
                        <a href="/icuc_ars/app/departments/index " class="nav-link "> <i class="ph-house"></i> <span data-key="t-holidays">Departments</span> </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link collapsed" href="/icuc_ars/app/notifications">
                            <i class="ph-chat"></i> <span data-key="t-dashboards">Notifications</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link collapsed" href="/icuc_ars/app/attendance_check">
                            <i class="ph-clock"></i> <span data-key="t-dashboards">Attendance Check</span>
                        </a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link menu-link collapsed" href="/icuc_ars/app/system_cleanup/index">
                            <i class="fas fa-user-cog"></i> <span data-key="t-dashboards">System Cleanup</span>
                        </a>
                    </li> -->
            </div>
            <!-- Sidebar -->
        </div>
    </div>
    <!-- <div class="sidebar-background bg-success"></div> -->
</div>