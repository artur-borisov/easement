<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>USSDGW<?php if (isset($_SERVER["REMOTE_USER"])) {print " :: ".$_SERVER["REMOTE_USER"];} ?></title>
    <!-- Grid -->
    <link href="../codebase/dhtmlxgrid.css" rel="stylesheet" type="text/css"/>
    <script src="../codebase/dhtmlxgrid.js"></script>
    <!-- Tabs -->
    <link href="../codebase/dhtmlxtabbar.css" rel="stylesheet" type="text/css"/>
    <script src="../codebase/dhtmlxtabbar.js" type="text/javascript"></script>
    <script>
        var TabbarUSSDGW;
        var GridRouteTable;

        function createGridRouteTable(divGrid) {
            strongFilter = function() {
                var input = this.value;
                return function(value, id){
                    if(input === '') return true;
                    return value.toLowerCase() === input.toLowerCase();
                };
            };

            var grid = new dhtmlXGridObject(divGrid);
            grid.setImagePath("../codebase/imgs/");
            grid.setHeader("GT_HLR_MASK,IMSI_MASK,MSISDN_MASK,RN,CALLINGGT,CALEDGT,OPERATOR,GT_HLR_GROUP_NAME");
            grid.attachHeader("#combo_filter,#combo_filter,#combo_filter,#combo_filter,#combo_filter,#combo_filter,#combo_filter,#combo_filter");
            grid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro");
            grid.setColAlign("left,left,left,left,left,left,left,left");
            grid.setColSorting("str,str,str,str,str,str,str,str"); 
            grid.setInitWidthsP("12,12,12,6,14,14,15,*");
            grid.init();

            return grid;
        }

        function loadGridData(tab,grid,URL,firstLoad,typeData) {
            tab.progressOn();
            if (firstLoad == true) {
                grid.load(URL,doOnGridLoaded,typeData);
            } else {
                if (typeData == undefined || typeData.toUpperCase() == "XML") {
                    grid.updateFromXML(URL,true,true,doOnGridLoaded);
                } else {
                    grid.clearAndLoad(URL, doOnGridLoaded,typeData);
                }
            }
            
            function doOnGridLoaded() {
                tab.progressOff();
            }        
        }

        
        function doOnLoad() {

            TabbarUSSDGW = new dhtmlXTabBar("tabbar");
            TabbarUSSDGW.setArrowsMode("auto");
            TabbarUSSDGW.addTab("tab1", "USSDGW - Маршрутизация", null, null, true);
            TabbarUSSDGW.tabs("tab1").attachObject("grid1");

            GridUSSDGWRouteTable = createGridRouteTable('grid1');
            loadGridData(TabbarUSSDGW.tabs("tab1"),GridUSSDGWRouteTable,"getdata.php?show=ROUTE_TABLE",true);

        }
        


    </script>  
</head>
<body onload="doOnLoad();">
  
    <div id="tabbar" style="width:100%; height:98vh;"></div>
    <div id="grid1" style="display: none; width:100%; height:100%;"></div>
</body>
</html>
