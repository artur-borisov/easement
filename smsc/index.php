<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>SMSC<?php if (isset($_SERVER["REMOTE_USER"])) {print " :: ".$_SERVER["REMOTE_USER"];} ?></title>
    <!-- Grid -->
    <link href="../codebase/dhtmlxgrid.css" rel="stylesheet" type="text/css"/>
    <script src="../codebase/dhtmlxgrid.js"></script>
    <!-- Tabs -->
    <link href="../codebase/dhtmlxtabbar.css" rel="stylesheet" type="text/css"/>
    <script src="../codebase/dhtmlxtabbar.js" type="text/javascript"></script>
    <script>
        var TabbarSMSC;
        var GridSMSC0101;
        var GridSMSC0102;
        var GridSMSC0201;
        var GridSMSC0301;

        function createGridDeliveryScheme(divGrid) {
            strongFilter = function() {
                var input = this.value;
                return function(value, id){
                    if(input === '') return true;
                    return value.toLowerCase() === input.toLowerCase();
                };
            };

            var grid = new dhtmlXGridObject(divGrid);
            grid.setImagePath("../codebase/imgs/");
            grid.setHeader("DELIVERY_NM,DELIVERY_AB,ATTEMPT,NETWORK_NM,ERROR_ID,ERROR_MAP,ERROR_NM,DELAY,DELAY_SRC");
            grid.attachHeader("#combo_filter,#combo_filter,#combo_filter,#select_filter,#select_filter,#text_filter,#select_filter,#numeric_filter,#combo_filter");
            grid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro");
            grid.setColAlign("left,left,left,left,right,left,left,right,left");
            grid.setColSorting("str,str,str,str,int,str,str,int,str"); 
            grid.setInitWidthsP("10,15,8,8,8,8,*,10,15");
            grid.init();
            grid.getFilterElement(3)._filter = strongFilter;
            grid.getFilterElement(4)._filter = strongFilter;
            grid.getFilterElement(6)._filter = strongFilter;
            
            return grid;
        }

        function createGridRoute(divGrid) {
            var grid = new dhtmlXGridObject(divGrid);
            grid.setImagePath("../codebase/imgs/");
            grid.setHeader("ADDRESS_RANGE,SYSTEMID_NM,CAPACITY_NM");
            grid.attachHeader("#combo_filter,#combo_filter,#combo_filter");
            grid.setColTypes("ro,ro,ro");
            grid.setColAlign("right,left,left");
            grid.setColSorting("str,str,str"); 
            grid.setInitWidthsP("30,30,*");
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

            TabbarSMSC = new dhtmlXTabBar("tabbar");
            TabbarSMSC.setArrowsMode("auto");
            TabbarSMSC.addTab("tab0101", "SMSC01 - Схема доставки", null, null, true);
            TabbarSMSC.addTab("tab0102", "SMSC01 - Маршутизация для \\d{3,10}:0:1");
            TabbarSMSC.addTab("tab0201", "SMSC02 - Схема доставки");
            TabbarSMSC.addTab("tab0301", "SMSC03 - Схема доставки");
            TabbarSMSC.tabs("tab0101").attachObject("grid0101");
            TabbarSMSC.tabs("tab0102").attachObject("grid0102");
            TabbarSMSC.tabs("tab0201").attachObject("grid0201");
            TabbarSMSC.tabs("tab0301").attachObject("grid0301");

            GridSMSC0101 = createGridDeliveryScheme('grid0101');
            loadGridData(TabbarSMSC.tabs("tab0101"),GridSMSC0101,"getdata.php?show=DELIVERY_SCHEME01",true);

            GridSMSC0102 = createGridRoute('grid0102');
            loadGridData(TabbarSMSC.tabs("tab0102"),GridSMSC0102,"smsc01-route.csv",true,"csv");

            GridSMSC0201 = createGridDeliveryScheme('grid0201');
            loadGridData(TabbarSMSC.tabs("tab0201"),GridSMSC0201,"getdata.php?show=DELIVERY_SCHEME02",true);

            GridSMSC0301 = createGridDeliveryScheme('grid0301');
            loadGridData(TabbarSMSC.tabs("tab0301"),GridSMSC0301,"getdata.php?show=DELIVERY_SCHEME03",true);
        }
        


    </script>  
</head>
<body onload="doOnLoad();">
  
    <div id="tabbar" style="width:100%; height:98vh;"></div>
    <div id="grid0101" style="display: none; width:100%; height:100%;"></div>
    <div id="grid0102" style="display: none; width:100%; height:100%;"></div>
    <div id="grid0201" style="display: none; width:100%; height:100%;"></div>
    <div id="grid0301" style="display: none; width:100%; height:100%;"></div>
<!--
<input type="button" value="Refersh 1" onclick='loadGridData(TabbarSMSC.tabs("tab0101"),GridSMSC0101,"getdata.php?db=SMSC01&show=T2_DELIVERY_SCHEME",false);'>
<input type="button" value="Refersh 2" onclick='loadGridData(TabbarSMSC.tabs("tab0102"),GridSMSC0102,"smsc01-route.csv",false,"csv");'>
<input type="button" value="Refersh 3" onclick='loadGridData(TabbarSMSC.tabs("tab0201"),GridSMSC0201,"getdata.php?db=SMSC02&show=T2_DELIVERY_SCHEME",false);'>
<input type="button" value="Refersh 4" onclick='loadGridData(TabbarSMSC.tabs("tab0301"),GridSMSC0201,"getdata.php?db=SMSC03&show=T2_DELIVERY_SCHEME",false);'>
-->
</body>
</html>
