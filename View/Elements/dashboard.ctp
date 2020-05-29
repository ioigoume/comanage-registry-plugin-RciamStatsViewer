<div id="dashboardTab">
    <h1><?php print _txt('pl.rciamstatsviewer.summary'); ?></h1>
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
        </div>
        <!-- ./col -->
    </div>
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?php print _txt('pl.rciamstatsviewer.dashboard.overall') ?></h3>
        </div>
        <div class="lineChart" id="loginsDashboard">
            <div id="line_div"></div>
            <div id="control_div" style="height:50px"></div>
        </div>
    </div>
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?php print _txt('pl.rciamstatsviewer.dashboard.idp') ?></h3>
        </div>
        <div class="pieChart idpPieChart" id="summaryIdPChart"></div>
    </div>
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?php print _txt('pl.rciamstatsviewer.dashboard.sp') ?></h3>
        </div>
        <div class="pieChart spPieChart" id="summarySpChart"></div>
    </div>
    <!-- /.box-header -->
    <div class="box" data-type="dashboard">
    <div class="box-header with-border">
            <h3 class="box-title"><?php print _txt('pl.rciamstatsviewer.dashboard.logins') ?></h3>
        </div>
    
        <div class="box-body dataTableWithFilter">
            <?php if ($vv_permissions['registered']) : ?>
                <div class="dataTableDateFilter bg-box-silver">
                    From: &nbsp;<input type="text" id="dashboardDateFrom" name="dashboardDateFrom" data-provide="datepicker" />
                    &nbsp;&nbsp;&nbsp;To: &nbsp;<input type="text" id="dashboardDateTo" name="dashboardDateTo" data-provide="datepicker" />
                    &nbsp;
                    <div class="btn-group">
                        &nbsp;<button type="button" class="btn btn-default dropdown-toggle filter-button" data-toggle="dropdown">
                            Filter <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="#" onclick="return false;" class="groupDataByDate" data-value="daily">Daily Basis</a></li>
                            <li><a href="#" onclick="return false;" class="groupDataByDate" data-value="weekly">Weekly Basis</a></li>
                            <li><a href="#" onclick="return false;" class="groupDataByDate" data-value="monthly">Monthly Basis</a></li>
                            <li><a href="#" onclick="return false;" class="groupDataByDate" data-value="yearly">Yearly Basis</a></li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
            <div class="dataTableContainer" id="dashboardDatatableContainer"></div>
        </div>
        <!-- /.box-body -->
    </div>
</div>