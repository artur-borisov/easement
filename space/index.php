<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>SPACE<?php if (isset($_SERVER["REMOTE_USER"])) {print " :: ".$_SERVER["REMOTE_USER"];} ?></title>
    <!-- Grid -->
    <link href="../codebase/dhtmlxgrid.css" rel="stylesheet" type="text/css"/>
    <script src="../codebase/dhtmlxgrid.js"></script>
    <!-- Tabs -->
    <link href="../codebase/dhtmlxtabbar.css" rel="stylesheet" type="text/css"/>
    <script src="../codebase/dhtmlxtabbar.js" type="text/javascript"></script>
    <script>
        var TabbarSPACE;
        var GridSPACEProviders;
        var GridSPACEServices;
        var GridSMSC;

        function createGridSPACEProviders(divGrid) {
            strongFilter = function() {
                var input = this.value;
                return function(value, id){
                    if(input === '') return true;
                    return value.toLowerCase() === input.toLowerCase();
                };
            };

            var grid = new dhtmlXGridObject(divGrid);
            grid.setImagePath("../codebase/imgs/");
            grid.setHeader("Наименование,Статус,E-mail,Примечание,Дата регистрации,Дата модификации,system_id,Пароль (hash),Транспорт");
            grid.attachHeader("#text_filter,#select_filter,#text_filter,#text_filter,&nbsp;,&nbsp;,#combo_filter,#text_filter,#combo_filter");
            grid.setColTypes("ro,ro,ro,ro,dhxCalendar,dhxCalendar,edtxt,edtxt,ro");
            grid.setColAlign("left,left,left,left,left,left,left,left,left");
            grid.setColSorting("str,str,str,str,date,date,str,str,str");
            grid.setDateFormat("%d.%m.%Y %H:%i", "%d.%m.%Y %H:%i");
            grid.setInitWidthsP("20,7,10,*,6,6,6,14,7");
            grid.init();
            grid.getFilterElement(1)._filter = strongFilter;

            return grid;
        }

        function createGridSPACEServices(divGrid) {
            strongFilter = function() {
                var input = this.value;
                return function(value, id){
                    if(input === '') return true;
                    return value.toLowerCase() === input.toLowerCase();
                };
            };

            var grid = new dhtmlXGridObject(divGrid);
            grid.setImagePath("../codebase/imgs/");
            grid.setHeader("Наименование провайдера,Статус провайдера,Наименование услуги,Статус услуги,Описание услуги,Номер услуги,Номер для переадресации по умолчанию,Тип трафика,Транспорт,system_id,unlimPush (атрибут),confirmAoC (атрибут),tarifficationType (атрибут),chargeLevel (атрибут),Стоимость (с НДС),Стоимость (без НДС)");
            grid.attachHeader("#text_filter,#select_filter,#text_filter,#select_filter,#text_filter,#select_filter,#select_filter,#select_filter,#select_filter,#select_filter,#select_filter,#select_filter,#select_filter,#select_filter,#numeric_filter,#numeric_filter");
            grid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ron,ron");
            grid.setColAlign("left,left,left,left,left,left,left,left,left,left,left,left,left,left,right,right");
            grid.setColSorting("str,str,str,str,str,str,str,str,str,str,str,str,str,str,int,int"); 
            grid.setInitWidthsP("*,6,6,6,6,6,6,6,6,5,5,5,6,5,6,6");
            grid.init();
            grid.getFilterElement(1)._filter = strongFilter;
            grid.getFilterElement(3)._filter = strongFilter;
            grid.getFilterElement(5)._filter = strongFilter;
            grid.getFilterElement(6)._filter = strongFilter;
            grid.getFilterElement(7)._filter = strongFilter;
            grid.getFilterElement(8)._filter = strongFilter;
            grid.getFilterElement(9)._filter = strongFilter;
            grid.getFilterElement(10)._filter = strongFilter;
            grid.getFilterElement(11)._filter = strongFilter;
            grid.getFilterElement(12)._filter = strongFilter;
            grid.getFilterElement(13)._filter = strongFilter;

            return grid;
        }

        function createGridSMSCRoute(divGrid) {
            var grid = new dhtmlXGridObject(divGrid);
            grid.setImagePath("../codebase/imgs/");
            grid.setHeader("Номер / диапазон номеров,system_id,capacity");
            grid.attachHeader("#combo_filter,#combo_filter,#combo_filter");
            grid.setColTypes("ro,ro,ro");
            grid.setColAlign("right,left,left");
            grid.setColSorting("str,str,str"); 
            grid.setInitWidthsP("30,30,*");
            grid.init();
            
            return grid;
        }

        function createGridInvoiceCost(divGrid) {
            strongFilter = function() {
                var input = this.value;
                return function(value, id){
                    if(input === '') return true;
                    return value.toLowerCase() === input.toLowerCase();
                };
            };

            var grid = new dhtmlXGridObject(divGrid);
            grid.setImagePath("../codebase/imgs/");
            grid.setHeader("Номер услуги,Тарифный план,Филиал,Услуга,Тип вызова,Лог. тип вызова,Метод вычисления соединения,Стоимость соединения,Провайдер");
            grid.attachHeader("#select_filter,#select_filter,#select_filter,#select_filter,#select_filter,#select_filter,#select_filter,#numeric_filter,#text_filter");
            grid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ron,ro");
            grid.setColAlign("left,left,left,left,left,left,left,left,left");
            grid.setColSorting("str,str,str,str,str,str,str,int,str"); 
            grid.setInitWidthsP("10,*,10,10,10,10,10,10,10");
            grid.init();
            grid.getFilterElement(1)._filter = strongFilter;
            grid.getFilterElement(2)._filter = strongFilter;
            grid.getFilterElement(3)._filter = strongFilter;
            grid.getFilterElement(4)._filter = strongFilter;
            grid.getFilterElement(5)._filter = strongFilter;
            grid.getFilterElement(6)._filter = strongFilter;
            grid.getFilterElement(7)._filter = strongFilter;

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

        function doOnLoad(){
            TabbarSPACE = new dhtmlXTabBar("tabbar");
            TabbarSPACE.setArrowsMode("auto");
            TabbarSPACE.addTab("tab1", "SPACE - Провайдеры");
            TabbarSPACE.addTab("tab2", "SPACE - Услуги", null, null, true);
            TabbarSPACE.addTab("tab3", "SMSC01 - Маршутизация для \\d{3,10}:0:1");
            TabbarSPACE.addTab("tab4", "INVOICE - Стоимость");
            TabbarSPACE.tabs("tab1").attachObject("grid1");
            TabbarSPACE.tabs("tab2").attachObject("grid2");
            TabbarSPACE.tabs("tab3").attachObject("grid3");
            TabbarSPACE.tabs("tab4").attachObject("grid4");

            GridSPACEProviders = createGridSPACEProviders('grid1');
            loadGridData(TabbarSPACE.tabs("tab1"),GridSPACEProviders,"getdata.php?show=PROVIDERS",true);

            GridSPACEServices = createGridSPACEServices('grid2');
            loadGridData(TabbarSPACE.tabs("tab2"),GridSPACEServices,"getdata.php?show=SERVICES",true);

            GridSMSC = createGridSMSCRoute('grid3');
            loadGridData(TabbarSPACE.tabs("tab3"),GridSMSC,"smsc01-route.csv",true,"csv");
            
            GridInvoiceCost = createGridInvoiceCost('grid4');
            loadGridData(TabbarSPACE.tabs("tab4"),GridInvoiceCost,"getdata.php?show=INVOICECOST",true);
        }


    </script>  
</head>
<body onload="doOnLoad();">
  
    <div id="tabbar" style="width:100%; height:98vh;"></div>
    <div id="grid1" style="display: none; width:100%; height:100%;"></div>
    <div id="grid2" style="display: none; width:100%; height:100%;"></div>
    <div id="grid3" style="display: none; width:100%; height:100%;"></div>
    <div id="grid4" style="display: none; width:100%; height:100%;"></div>
<!--
<input type="button" value="Refersh 1" onclick='loadGridData(TabbarSPACE.tabs("tab1"),GridSPACEServices,"getdata.php?db=SPACE01&show=T2_SERVICES",false);'>
<input type="button" value="Refersh 2" onclick='loadGridData(TabbarSPACE.tabs("tab2"),GridSMSC,"smsc01-route.csv",false,"csv");'>
-->
</body>
</html>
