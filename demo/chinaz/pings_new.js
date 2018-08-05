/*
* SVG地图
* */
function SVG_Map_China(id, width, height, title) {
    this.mapdata = MapData();//地图数据
    this.id = id;
    this.svgcontent = document.getElementById(id);
    this.width = width;
    this.height = height;
    this.temp = '<div class="svggroup pr"></div>';
    this.temp_ie = '<div class="svggroup pr"><rvml:group style="position : absolute; /*width: 730px; height: 533px;*/ top: 0px; left: -1px" coordsize="{0},{1}" class="rvml" id="vmlgroup_{2}"><rvml:group></div>'.format(width, height, id);
    this.temp_tip = '<div class="stateTip" style="position:absolute;border:2px solid #AFABAA;background:#fff;border-radius:5px;padding:5px 10px;z-index:99"></div>';
    this.defaultsAttr = { fill: '#fff', stroke: '#aaaaaa', 'stroke-width': '1px', cursor: 'pointer' };
    this.stateTipX = 0;
    this.stateTipY = -20;
    this.stateTipWidth = sys.ie ? '120' : 'auto';
    this.title = title;
    var legendStyle = { 'cursor': 'pointer', 'stroke-width': '0' };
    this.legend_ping = {
        item0: { attrs: $.extend({ fill: "#24AA1D" }, legendStyle), txt: "<=50ms" },
        item1: { attrs: $.extend({ fill: "#42DD3F" }, legendStyle), txt: "50~100ms" },
        item2: { attrs: $.extend({ fill: "#BEF663" }, legendStyle), txt: "100~150ms" },
        item3: { attrs: $.extend({ fill: "#F6ED44" }, legendStyle), txt: "150~200ms" },
        item4: { attrs: $.extend({ fill: "#F69833" }, legendStyle), txt: ">200ms" },
        item5: { attrs: $.extend({ fill: "#E61610" }, legendStyle), txt: "超时" }
    };
    this.legend_test = {
        item0: { attrs: $.extend({ fill: "#24AA1D" }, legendStyle), txt: "<=400ms" },
        item1: { attrs: $.extend({ fill: "#42DD3F" }, legendStyle), txt: "400~800ms" },
        item2: { attrs: $.extend({ fill: "#BEF663" }, legendStyle), txt: "800~1200ms" },
        item3: { attrs: $.extend({ fill: "#F6ED44" }, legendStyle), txt: "1200~1600ms" },
        item4: { attrs: $.extend({ fill: "#F69833" }, legendStyle), txt: ">1600ms" },
        item5: { attrs: $.extend({ fill: "#E61610" }, legendStyle), txt: "超时" }
    };
    return this;
};
SVG_Map_China.prototype.getContents = function () {
    getBgimg();
    if (!this.svgcontent) return;
    var nestedWrapper;
    if (Raphael.type == "VML") {
        this.svgcontent.innerHTML = this.temp_ie
        nestedWrapper = document.getElementById("vmlgroup_" + this.id);
    } else {
        this.svgcontent.innerHTML = this.temp;
        nestedWrapper = this.svgcontent.getElementsByClassName("svggroup")[0];;
    }
    var paper = Raphael(nestedWrapper);
    var vmlDiv;
    if (Raphael.type == "SVG") {
        paper.canvas.setAttribute("viewBox", "5 1 " + this.width + " " + this.height);
        paper.canvas.removeAttribute("width");
        paper.canvas.removeAttribute("height");
    } else {
        vmlDiv = this.svgcontent.getElementsByTagName("div")[0];
    }
    paper.text(20, 10, this.title).attr({ fill: "#000", "font-size": "22px", "text-anchor": "start" });
    var _this = this;
    var pathElms = {};
    $(this.svgcontent).addClass("mapbg-img");
    for (var item in this.mapdata) {
        var d = this.mapdata[item];
        var _path = paper.path(d.path);
        _path.name = item;
        _path.areas = d.areas;
        _path.attr(this.defaultsAttr);

        //if (_path.name == "香港" || _path.name == "澳门") {
        /*********************太卡  暂时抛弃 BEGIN**********************/
        //var _text = this.areasText(d, paper, _path);//有BUG  暂时不调用
        //  _text.name = item;
        //  _text.areas = d.areas;
        //  _this.eventBind(_path, _text);
        /*************************END***********************************/
        //} else
        _this.eventBind(_path);

        var obj = JSON.parse('{"' + item + '":{}}');
        $.extend(obj[item], { elm: _path });
        $.extend(pathElms, obj);
    }
    this.createLegend(paper);


    return { paper: paper, pathElms: pathElms };
};
/*
* 生成图例
* */
SVG_Map_China.prototype.createLegend = function (paper) {
    var width = $(this.svgcontent).width();
    var x = 28;//图例x坐标
    var y = 24;//图例y坐标
    var w = 15;//图例宽
    var h = 15;//图例高
    var text_x = x + y;//文本的x坐标
    var i = 15;//用于计算图例的位置
    var j = 29.3;//用于计算生成文本的位置
    var legend;
    if ($("#ftype").val() != "testcom")
        legend = this.legend_ping;
    else {
        y = 28;//图例y坐标
        w = 18;//图例宽
        h = 18;//图例高
        text_x = x + y;//文本的x坐标
        i = 8.2;//用于计算图例的位置
        j = 15.8;//用于计算生成文本的位置
        legend = this.legend_test;
    }
    for (var item in legend) {
        var o = legend[item];
        var rect = paper.rect(x, y, w, h).attr($.extend(o.attrs, { y: width - y * i }));
        rect.name = item;
        var text = paper.text(x, y, o.txt).attr({ fill: "#000", x: text_x, y: width - y / 2 * j, 'cursor': 'pointer', "font-size": "14px", "text-anchor": "start" });
        text.name = item;
        i--;
        j -= 2;
        this.eventLegend(rect, text);

    }
};
/*
* 图例(文本)事件绑定
* */
SVG_Map_China.prototype.eventLegend = function (rect, text) {
    var _this = this;
    rect.mouseover(function (e) {
        _this.getLegendAreas_over(this);
    });
    rect.mouseout(function (e) {
        _this.getLegendAreas_out(this);
    });
    text.mouseover(function (e) {
        _this.getLegendAreas_over(this, rect);
    });
    text.mouseout(function (e) {
        _this.getLegendAreas_out(this, rect);
    });
};
/*
* 图例焦点数据绑定
* */
SVG_Map_China.prototype.getLegendAreas_over = function (obj, rect) {
    obj = rect || obj;
    var ftype = $("#ftype").val();
    $(obj.node).animate({ rx: '10', ry: '10', lx: '10', ly: '10' }, 200);
    for (var item in this.obj_contents.pathElms) {
        if (this.mapdata[item].value !== undefined) {
            var v = this.obj_contents.pathElms[item].avgValue;
            if (obj.name == 'item0' && v > 0 && v <= (ftype == "ping" ? 50 : 400))
                this.obj_contents.pathElms[item].elm.stop().animate({ fill: '#55A7E3' }, 200);
            else if (obj.name == 'item1' && (v > (ftype == "ping" ? 50 : 400) && v <= (ftype == "ping" ? 100 : 800)))
                this.obj_contents.pathElms[item].elm.stop().animate({ fill: '#55A7E3' }, 200);
            else if (obj.name == 'item2' && (v > (ftype == "ping" ? 100 : 800) && v <= (ftype == "ping" ? 150 : 1200)))
                this.obj_contents.pathElms[item].elm.stop().animate({ fill: '#55A7E3' }, 200);
            else if (obj.name == 'item3' && (v > (ftype == "ping" ? 150 : 1200) && v <= (ftype == "ping" ? 200 : 1600)))
                this.obj_contents.pathElms[item].elm.stop().animate({ fill: '#55A7E3' }, 200);
            else if (obj.name == 'item4' && v > (ftype == "ping" ? 200 : 1600))
                this.obj_contents.pathElms[item].elm.stop().animate({ fill: '#55A7E3' }, 200);
            else if (obj.name == 'item5' && v == 0)
                this.obj_contents.pathElms[item].elm.stop().animate({ fill: '#55A7E3' }, 200);
        }
    }
};
/*
* 图例失去焦点数据还原
* */
SVG_Map_China.prototype.getLegendAreas_out = function (obj, rect) {
    obj = rect || obj;
    $(obj.node).animate({ rx: '0', ry: '0', lx: '0', ly: '0' }, 200);
    for (var item in this.obj_contents.pathElms) {
        if (this.mapdata[item].value !== undefined) {
            var v = this.mapdata[item].value > 0 ? (this.mapdata[item].value / this.mapdata[item].serverCount) : 0;
            this.obj_contents.pathElms[item].elm.stop().animate({ fill: getItemBg(v) }, 200);
        }
    }
};
/*
* 填充省份/地区名称
* */
SVG_Map_China.prototype.areasText = function (d, paper, path) {
    var bbox = path.getBBox();
    var xx = bbox.x + (bbox.width / 2);
    var yy = bbox.y + (bbox.height / 2);
    switch (d['areas']) {
        case "江苏": xx += 5; yy -= 10; break;
        case "河北": xx -= 10; yy += 20; break;
        case "天津": xx += 10; yy += 10; break;
        case "上海": xx += 10; break;
        case "广东": yy -= 10; break;
        case "澳门": yy += 10; break;
        case "香港": xx += 20; yy += 5; break;
        case "甘肃": xx -= 40; yy -= 30; break;
        case "陕西": xx += 5; yy += 10; break;
        case "内蒙古": xx -= 15; yy += 65; break;
    }
    var _text = paper.text(xx, yy, path.areas).attr({
        'fill': '#b95f4f',
        'font-size': '16px',
        'font-weight': 'bold',
        'cursor': 'pointer'
    });
    return _text;
};
/*
* 绑定事件
* */
SVG_Map_China.prototype.eventBind = function (path, text) {
    var _this = this;
    path.mouseover(function (e) {
        _this.showTip(e, this);
        if (!_this.mapdata[this.name].value) return;
        this.stop().animate({ fill: '#55A7E3' }, 200);
    });
    path.mouseout(function (e) {
        $('.stateTip').remove();
        if (!_this.mapdata[this.name].value) return;
        this.stop().animate({ fill: getItemBg(_this.mapdata[this.name].value > 0 ? (_this.mapdata[this.name].value / _this.mapdata[this.name].serverCount) : 0) }, 200);
    });
    path.click(function (e) {
        if ($("#ftype").val() == "test") {
            getOneInfo_speedTest(this)
        } else {
            getOneInfo_ping(this);
        }
    });
    path.mousemove(function (e) {
        _this.showTip(e, this);
    });
    if (text) {
        text.mouseover(function (e) {
            _this.showTip(e, this);
            if (!_this.mapdata[this.name].value) return;
            path.stop().animate({ fill: '#55A7E3' }, 200);
        });
        text.mouseout(function (e) {
            $('.stateTip').remove();
            if (!_this.mapdata[this.name].value) return;
            path.stop().animate({ fill: getItemBg(_this.mapdata[this.name].value > 0 ? (_this.mapdata[this.name].value / _this.mapdata[this.name].serverCount) : 0) }, 200);
        });
        text.click(function (e) {
            if ($("#ftype").val() == "test") {
                getOneInfo_speedTest(path)
            } else {
                getOneInfo_ping(path);
            }
        });
        text.mousemove(function (e) {
            _this.showTip(e, path);
        });
    }
};
/*
* 提示信息框
* */
SVG_Map_China.prototype.showTip = function (e, obj) {
    if ($('.stateTip').length == 0) {
        $(this.svgcontent).append(this.temp_tip);
    }
    var data = this.mapdata[obj.name].data;
    $('.stateTip').html('');
    $('.stateTip').append('<p style="font-weight: bold">' + obj.name + '</p>');
    if (data.length) {
        for (var i = 0; i < data.length; i++) {
            var item = data[i];
            $('.stateTip').append('<p{0}>{1}：{2}</p>'.format(item.value == "0" ? " class=\"col-red\"" : "", item.type.replace(obj.name, ''), (item.value == "0" ? "超时" : (item.value == "0.5" ? "<1" : item.value)) + (item.value == "0" ? "" : "ms")));
        }
    } else
        $('.stateTip').append('<p class="col-red">暂无检测结果</p>');
    var _offsetXY = this.getOffset(e);
    $('.stateTip').css({
        width: this.stateTipWidth,
        left: _offsetXY.x,
        top: _offsetXY.y
    }).show();
};
/*
* 提示信息框定位
* */
SVG_Map_China.prototype.getOffset = function (e) {
    var mouseX,
            mouseY,
            tipWidth = $('.stateTip').width(),
            tipHeight = $('.stateTip').height();
    if (e && e.pageX) {
        mouseX = e.pageX;
        mouseY = e.pageY;
    } else {
        mouseX = event.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
        mouseY = event.clientY + document.body.scrollTop + document.documentElement.scrollTop;
    }
    if (event.clientY < tipHeight)
        mouseY += tipHeight - this.stateTipY * 2;//超出可视范围
    mouseX = mouseX - tipWidth / 2 + this.stateTipX < 0 ? this.svgcontent.offsetLeft : mouseX - tipWidth / 2 + this.stateTipX;
    mouseY = mouseY - tipHeight + this.stateTipY < 0 ? mouseY - this.stateTipY : mouseY - tipHeight + this.stateTipY;
    return { x: mouseX, y: mouseY };
};
//调用生成地图
var svgmapchina = new SVG_Map_China('charts', 767, 560, $("#host").val());
svgmapchina.obj_contents = svgmapchina.getContents();

var svgmapchina1 = new SVG_Map_China('charts1', 767, 560, $("#host1").val());
svgmapchina1.obj_contents = svgmapchina1.getContents();

if (!sys.ie <= 8) {
    $(window).resize(function () {
        setTimeout(function () {
            getBgimg()
        });
    });
}
$(function () {
    getBgimg()
});
function getBgimg() {
    if ($("body").hasClass("pusmall")) {
        $("#charts").removeClass("mapbg-730").addClass("mapbg-600");
        if ($("#charts1"))
            $("#charts1").removeClass("mapbg-730").addClass("mapbg-600");
    }
    else {
        $("#charts").removeClass("mapbg-600").addClass("mapbg-730");
        if ($("#charts1"))
            $("#charts1").removeClass("mapbg-600").addClass("mapbg-730");
    }
}
/***********************三八线**************************/

function Pings(type) {
    this.thread = type == "speedcom" ? 10 : 5; //每次ajax线程数 网站测速对比时 每次10条 否则5条
    this.loadimg = '<img src="' + imgurlbase + '/public/spinner.gif"/>正在加载...';
    this.enkey = $("#enkey").val();
    this.host = $("#host").val();
    this._host = $("#host1").val();
    this.ishost = $("#ishost").val();
    this.isertry = 0; //是不是点击重试
    this.list = $("#speedlist div.listw[state]");
    this.cnt = 0;
    this.iparr = [];
    this.type = type; //调用页面 : ping/speedtest/speedcom/speedworld
    this.xyiplist = []; //相应时间IP
    this.checktype = $("#checktype").val(); //0:ping  1:测速
    this.sorttype = 0; //排序类型
    this.seriesData = []; //电信
    this.seriesData1 = []; //多线
    this.seriesData2 = []; //联通
    this.seriesData3 = []; //移动
    this._seriesData = []; //电信
    this._seriesData1 = []; //多线
    this._seriesData2 = []; //联通
    this._seriesData3 = []; //移动
    this.areaAvgflag = true; //是否绑定地区平均值
    this.sortFlag = "test";

    //对接ECHARTS对应的属性
    this.ECHARTS; //Echarts类
    this.chart; //echarts对象
    this._ECHARTS; //Echarts类(网站测速对比时 存放第二个)
    this._chart; //echarts对象(网站测速对比时 存放第二个)
    this.toolWorldFormat = [];
    this.init();
};
var _pings = Pings.prototype; //定义公共的Pings原型
//应用主入口
Pings.prototype.init = function () {
    var _this = this;
    var openurl = "";
    switch (this.type) {
        case "ping":
            openurl = "speedtest";
            this.initPing();
            break;
        case "speedtest":
            openurl = "speedworld";
            this.initSpeedTest();
            break;
        case "speedworld":
            openurl = "speedtest";
            this.initSpeedWorld();
            break;
        case "speedcom":
            this.initSpeedCom();
            break;
    }
    $(".SeaBtnCut").click(function () {
        if (this.host != $("#host").val().trim())
            window.open("http://tool.chinaz.com/{0}/{1}".format(openurl, $("#host").val()));
        else
            window.open("http://tool.chinaz.com/{0}/{1}".format(openurl, this.host));
    });
    for (var i = 0; i < this.thread; i++)
        this.start();

    $(".allRetry").on("click", function () {
        if (_this.type == "speedcom") {
            $("a.retry:visible").parents("dl.row").attr("trycount", 0).attr("state", 0);
        } else {
            $("a.retry:visible").parents(".listw").attr("trycount", 0).attr("state", 0);
        }
        $(this).hide()
        for (var i = 0; i < _this.thread; i++)
            _this.start();
    });
};
//speedcom
Pings.prototype.initSpeedCom = function () {
    var host = this.host;
    window.onload = function () {
        $("#loc").append('<a href="http://tool.chinaz.com/speedtest/' + host + '" target=\"_blank\">国内网站测速</a><a href="http://tool.chinaz.com/speedworld/' + host + '" target=\"_blank\">海外网站测速</a>');
    };
    this.list = $("#speedlist div.row dl");
    $(".sortlist .showb").click(function () {
        var $pa = $(this).siblings(".ssub");
        $pa.find("i").hide();
        var pos = $(this).position();
        $(".sortlist .ssub").not($pa).fadeOut(200);
        var _this = $(this);
        $(".sortlist .showb").not(_this).removeClass("showb9");
        $pa.animate({ top: pos.top + 14, left: pos.left }, 200, function () {
            if ($(this).is(":hidden")) {
                $(this).stop().fadeIn(200);
                if (!($(this).find("i").hasClass("down") || $(this).find("i").hasClass("up")))
                    _this.addClass("showb9");
            }
            else {
                $(this).stop().fadeOut(200);
                _this.removeClass("showb9");
            }
        });
    });
    var _this = this;
    $(".sortlist .ssub a").click(function () {
        if ($(this).index() == 0) _this.sortFlag = "test";
        else _this.sortFlag = "test1";
        _this.PingSort($(this).find("i"));
        $(".sortlist .ssub").stop().fadeOut(200);
    });
    $(document).click(function (e) {
        var $target = $(e.target);
        if (!$target.hasClass("showb")) {
            $(".sortlist .showb").removeClass("showb9");
            $(".sortlist .ssub").stop().fadeOut(200);
        }
    });
    this.initSpeed();
};
//speedworld
Pings.prototype.initSpeedWorld = function () {
    var host = this.host;
    window.onload = function () {
        $("#loc").append('<a href="http://tool.chinaz.com/speedcom/' + host + '-chinaz.com" target=\"_blank\">网站速度对比</a><a href="http://tool.chinaz.com/speedtest/' + host + '" target=\"_blank\">国内网站测速</a>');
    };
    this.list = $("#speedlist div.listw");
    this.initSpeed();
};
//speedtest
Pings.prototype.initSpeedTest = function () {
    var host = this.host;
    window.onload = function () {
        $("#loc").append('<a href="http://tool.chinaz.com/speedcom/' + host + '-chinaz.com" target=\"_blank\">网站速度对比</a><a href="http://tool.chinaz.com/speedworld/' + host + '" target=\"_blank\">海外网站测速</a>');
    };
    this.linetypeClick();
    this.list = $("#speedlist div.listw[state]");
    this.initSpeed();
};
//网站测速入口public
Pings.prototype.initSpeed = function () {
    var _this = this;
    if (!sys.ie || sys.ie > 8)
        $(".MapChartOne").removeClass("autohide");
    this.checktype = 1;
    this.retryClick();
    $("a[ctype]").on("click", function () { showAreaItem($(this).attr("ctype")) });
    $("div.item-table").delegate("i.sort", "click", function () { _this.PingSort(this); });
    //调用地图
    //this.ECHARTS = new Echarts("charts", this.host);
    //this.ECHARTS.type = this.type;
    //this.chart = this.ECHARTS.init();
    //if (this.type == "speedcom") {
    //    this._ECHARTS = new Echarts("charts1", this._host);
    //    this._ECHARTS.type = this.type;
    //    this._chart = this._ECHARTS.init();
    //}
};
//Ping
Pings.prototype.initPing = function () {
    if (!sys.ie || sys.ie > 8)
        $(".PingCentWrap").removeClass("autohide");
    var _this = this;
    this.linetypeClick();
    this.list = $("#speedlist div.listw[state]");

    $("a[ctype]").on("click", function () { showAreaItem($(this).attr("ctype")) });
    $("#showiplist").click(function () {
        var txt = $(this).text();
        if (txt == "更多") {
            $(this).text("收起");
            $("#ipliststr").css("height", "auto");
        } else {
            $(this).text("更多");
            $("#ipliststr").css("height", "44px");
        }
    });
    $(".tabs").tabs({
        control: ".tabspanel",
        className: "curt",
        eventName: "click",
        childTag: "a",
        callback: function () {
            $(".tabs >a").removeClass("s-on");
            $(".item-show >.item-list").hide();
            var index = $(".tabs a.curt").index();

            $(".item-list:eq(" + index + ")").show();
            $(".tabs a.curt").addClass("s-on");
            if (_this.areaAvgflag) {
                if (index > 0) {
                    //_this.sorttype = 3;
                    _this.ping.getSelectBindAreaAvg(index); //电信/多线/联通/移动/海外地区平均值排序
                } else {
                    //_this.sorttype = -1;
                    _this.bindFastSlowAvg($("#speedlist div.row[state=2]")); //全部节点/线路最快最慢平均值
                }
            }
        }
    });
    this.retryClick();
    //显示列表中相关ip节点信息
    $("#ipliststr").delegate("a", "click", function () {
        $("#ipliststr a").not($(this)).attr("chk", "1").css("color", "#338de6");
        $(".item-list a").attr("chk", "1").css("color", "#338de6");
        _this.ping.toggleIP($(this).text(), this);
    });
    $("div.item-list").delegate("a.sip", "click", function () {
        $(".item-list a").not($(this)).attr("chk", "1").css("color", "#338de6");
        $("#ipliststr a").attr("chk", "1").css("color", "#338de6");
        var ip = $(this).text().replace("[", "").replace("]", "");
        _this.ping.toggleIP(ip, this);
    });
    $("div.item-table").delegate("i.sort", "click", function () { _this.PingSort(this); });
    this.ping.getCDN();
    //$(".PingCentWrap").delegate($("#getcdn"), "click", _this.ping.getCDN);

    //调用地图
    //this.ECHARTS = new Echarts("charts", _this.host);
    //this.ECHARTS.type = this.type;
    //this.chart = this.ECHARTS.init();

  
};
//选择测试类型
Pings.prototype.getlinetype = function () {
    var linetype = $("#linetype");
    linetype.val("");
    var ltarr = [];
    $("p.wbtnNode a.chk.actv").each(function () {
        ltarr.push($(this).text())
    });
    linetype.val(ltarr.join(','));
};
//点击选择路线
Pings.prototype.linetypeClick = function () {
    var _this = this;
    $("p.wbtnNode a.chk").click(function () {
        if ($(this).hasClass("actv")) {
            $(this).removeClass("actv");
            $("p.wbtnNode a.chkall").removeClass("actv");
        } else {
            $(this).addClass("actv");
            if ($("p.wbtnNode a.chk.actv").length == 5)
                $("p.wbtnNode a.chkall").addClass("actv");
        }
        _this.getlinetype();
    });
    $("p.wbtnNode a.chkall").click(function () {
        if (!$(this).hasClass("actv")) {
            $("p.wbtnNode a").addClass("actv");
        } else {
            $("p.wbtnNode a").removeClass("actv");
        }
        _this.getlinetype();
    });
};



//失败重查
Pings.prototype.retryClick = function () {
    var _this = this;
    $("#speedlist").delegate("a.retry", "click", function () {
        //_this.ECHARTS.isertry = 1;
        _this.seriesDataInit(this);
        if (_this.type == "speedcom")
            _this.ajaxPing($(this).parents("dl"));
        else
            _this.ajaxPing($(this).parents("div.row"));
    });
};
//启用Ajax
Pings.prototype.start = function () {
    for (var i = 0; i < this.list.length; i++) {
        var $li = $(this.list[i]);
        if ($li.attr("state") == 0 && $li.attr("trycount") < 3) {
            $li.attr("state", 1);
            this.ajaxPing($li);
            return false;
        }
    }
};
//Ajax返回Ping结果处理方法
Pings.prototype.ping = function (li, msg) {
    this._ping = this.ping;
    li.find("[name=ip]").html("<a href='http://ip.chinaz.com/?ip=" + msg.result.ip + "' target='_blank'>" + msg.result.ip + "</a>");
    li.find("[name=ipaddress]").html(msg.result.ipaddress.replace(/\s+/, "") ? msg.result.ipaddress : "&nbsp;");
    var sc = li.find("[name=city]").html();
    var guid = li.attr("id");
    if (this.FormatInt(msg.result.responsetime) >= 200) {
        li.find("[name=responsetime]").html("<font color=red>" + msg.result.responsetime.replace("毫秒", "ms") + "</font>");
        if (msg.result.responsetime.indexOf("超时") > 0) {
            this.cnt++;
            li.find("[name=ip]").attr("ipgroup", "-1");
            $("#gjd").text($("div.row[state=\"2\"]").length);
            if (!this.isertry) {
                if (parseInt(li.attr("trycount")) >= 2) {
                    //if (this.chart) {
                    var sd = this.getSeriesData(sc, "超时", this.seriesData, this.seriesData1, this.seriesData2, this.seriesData3, this.ECHARTS, guid);
                    //    this.ECHARTS.bindChart(sd, this.chart);
                    //}
                }
            }
        }
    }
    else
        li.find("[name=responsetime]").html(msg.result.responsetime.replace("毫秒", "ms"));
    li.find("[name=ttl]").html(msg.result.ttl);

    this._ping.getAloneIP(msg, this);

    if (this.xyiplist.length) {
        if (this.xyiplist.toString().indexOf(msg.result.ip) < 0)
            this.xyiplist.push(msg.result.ip);
    } else
        this.xyiplist.push(msg.result.ip);
    li.find("[name=ip]").attr("ipgroup", this.xyiplist.toString().indexOf(msg.result.ip));
    $("#gjd").text($("div.row[state=\"2\"]").length); //当前Ping完成节点数
    //if (this.chart) {
    var sd = this.getSeriesData(sc, msg.result.responsetime.replace("毫秒", ""), this.seriesData, this.seriesData1, this.seriesData2, this.seriesData3, this.ECHARTS, guid);
    //this.ECHARTS.bindChart(sd, this.chart);
    //bindChart(sd, this.chart);
    //}

};
var ping_fn = Pings.prototype.ping; //定义ping的原型变量
ping_fn._this = Pings.prototype; //在ping方法中定义_this指向Pings的原型
//Ping独立IP相关
ping_fn.getAloneIP = function (msg, _this) {
    _this.iparr.push(msg.result.ip);
    _this.cnt++;
    var iplist = _this.iparr.unique();
    $("#gip").text(iplist.length);
    $("#allip").removeClass("autohide");
    if (iplist.length > 8) {
        $("#showiplist").show();
    }
    var $iplist = $("#ipliststr");
    if ($iplist.text().indexOf(msg.result.ip) < 0) {
        $iplist.append("<a href=\"javascript:\">{0}</a>".format(msg.result.ip));
        $("#hidip").val($("#hidip").val() + "," + msg.result.ip);
        $("#copyip").attr("data-clipboard-text", $("#hidip").val())
    }
    if ($iplist.find("a").length >= 4 && $iplist.height() < 44)
        $iplist.css("height", "44px");


};
//Ping地区平均响应时间
ping_fn.bindAreaAvg = function (lilist, type) {
    var _this = this._this;
    var nlist = $("#speedlist div.row");
    for (var i = 0; i < lilist.length; i++) {
        var $b = $(lilist[i]).find("[tabs='area']");
        var $span = $(lilist[i]).find("span:eq(1)");
        var area = $b.text();
        var rtime = 0;
        var cnt = 0;
        for (var j = 0; j < nlist.length; j++) {
            var ncity = $(nlist[j]).find("div[name='city']").text();
            if (ncity.indexOf(area) >= 0) {//&& ncity.indexOf(type) > 0
                var time = $(nlist[j]).find("div[name='responsetime']").text();
                if (time == "<1ms") {
                    time = _this.FormatDouble("0.5") == 9999 ? 0 : _this.FormatDouble("0.5");
                } else
                    time = _this.FormatInt(time) == 9999 ? 0 : _this.FormatInt(time);
                rtime += time * 1;
                cnt++;
            }
        }
        var t = (rtime * 1 / cnt).toString().indexOf('.') > 0 ? (rtime * 1 / cnt).toFixed(1) : (rtime * 1 / cnt);

        if (parseFloat(t) >= 0.1 && parseFloat(t) <= 100)
            $span.attr("class", "col-green");
        else if (parseFloat(t) >= 101 && parseFloat(t) <= 200)
            $span.attr("class", "col-hint02");
        else
            $span.attr("class", "col-hint");
        if (t == 0)
            t = "-";
        else if (t < 1)
            t = "<1ms";
        else
            t = t + "ms";
        $span.text(t);
    }
};
ping_fn.getSelectBindAreaAvg = function (index) {
    switch (index) {
        case 1:
            this.bindAreaAvg($("#dianx>tbody tr td"), "电信");
            this.PingSortArea($("#dianx>tbody tr td"), $("#dianx>ul"));
            break;
        case 2:
            this.bindAreaAvg($("#duox>tbody tr td"), "多线");
            this.PingSortArea($("#duox>tbody tr td"), $("#duox>tbody"));
            break;
        case 3:
            this.bindAreaAvg($("#liant>tbody tr td"), "联通");
            this.PingSortArea($("#liant>tbody tr td"), $("#liant>tbody"));
            break;
        case 4:
            this.bindAreaAvg($("#yid>tbody tr td"), "移动");
            this.PingSortArea($("#yid>tbody tr td"), $("#yid>tbody"));
            break;
        case 5:
            this.bindAreaAvg($("#haiw>tbody tr td"), "海外");
            this.PingSortArea($("#haiw>tbody tr td"), $("#haiw>tbody"));
            break;
        default:
            this.bindAreaAvg($("#dianx>tbody tr td"), "电信");
            this.PingSortArea($("#dianx>tbody tr td"), $("#dianx>tbody"));
            this.bindAreaAvg($("#duox>tbody tr td"), "多线");
            this.PingSortArea($("#duox>tbody tr td"), $("#duox>tbody"));
            this.bindAreaAvg($("#liant>tbody tr td"), "联通");
            this.PingSortArea($("#liant>tbody tr td"), $("#liant>tbody"));
            this.bindAreaAvg($("#yid>tbody tr td"), "移动");
            this.PingSortArea($("#yid>tbody tr td"), $("#yid>tbody"));
            this.bindAreaAvg($("#haiw>tbody tr td"), "海外");
            this.PingSortArea($("#haiw>tbody tr td"), $("#haiw>tbody"));
    }
};
//CNAME信息
ping_fn.getCDN = function () {
    $("#getcdn").html('<img src="' + imgurlbase + '/public/spinner.gif"/>');
    jQuery.post("/ajaxseo.aspx?t=cdn", "host=" + $("#host").val(), function (data) {
        var cname, cdntype;
        if (data.error == 1) {
            $(".isrem").remove();
            $("#allip").before('<div class="PClipW clearfix isrem"><span class="mr10 w30-0 block fl">CDN信息获取异常<a href="javascript:" id="getcdn">重试</a></span></div>');
            return;
        }
        if (data.status == 0) {
            cname = "无";
            cdntype = "未知";
        } else {
            cname = data.result[0].ipcnt > 1 ? '<a href="http://tool.chinaz.com/dns?type=1&host=' + $("#host").val() + '" target="_blank">多IP解析</a>' : '无';
            cdntype = data.result[0].type == "未知" ? "未知" : '<a href="http://tool.chinaz.com/dns?type=5&host=' + $("#host").val() + '" target="_blank">' + data.result[0].type + '</a>';
            if (data.result[0].ipcnt > 1 && data.result[0].type != "未知") cname = "无";
        }
        $("#cname").html(cname);
        $("#cdntype").html(cdntype);
    }, "jsonp");
};
//全部节点/线路最快最慢平均值
Pings.prototype.bindFastSlowAvg = function ($all) {
    var $dianx = [], $duox = [], $liant = [], $yid = [], $haiw = [], $newall = []; //各正常节点
    var counttime_dianx = 0, counttime_duox = 0, counttime_liant = 0, counttime_yid = 0, counttime_haiw = 0, counttime_all = 0; //各正常节点耗时统计
    var count_dianx = 0, count_duox = 0, count_liant = 0, count_yid = 0, count_haiw = 0, count_all = 0; //各正常节数点统计
    for (var i = 0; i < $all.length; i++) {
        var $li = $($all[i]);
        var time = 9999;
        if (parseInt(this.ishost))
            time = this.FormatInt($li.find("[name=\"alltime\"]").text());
        else
            time = this.FormatInt($li.find("[name=\"responsetime\"]").text());
        if (time == 9999)
            continue;
        $newall.push($li);
        counttime_all += time;
        count_all++;
        var linetype;
        if (this.type == "speedcom") linetype = $li.parents("li").attr("linetype");
        else linetype = $li.attr("linetype");
        switch (linetype) {
            case "1":
                $dianx.push($li);
                counttime_dianx += time;
                count_dianx++;
                break;
            case "2":
                $duox.push($li);
                counttime_duox += time;
                count_duox++;
                break;
            case "3":
                $liant.push($li);
                counttime_liant += time;
                count_liant++;
                break;
            case "4":
                $yid.push($li);
                counttime_yid += time;
                count_yid++;
                break;
            case "5":
                $haiw.push($li);
                counttime_haiw += time;
                count_haiw++;
                break;
        }
    }
    this.sorttype = 2;
    //排序
    $newall.sort(this.SortDesc);
    $dianx.sort(this.SortDesc);
    $duox.sort(this.SortDesc);
    $liant.sort(this.SortDesc);
    $yid.sort(this.SortDesc);
    //赋值
    //this.setVal("所有", $newall, counttime_all, count_all);
    this.setVal("电信", $dianx, counttime_dianx, count_dianx);
    this.setVal("多线", $duox, counttime_duox, count_duox);
    this.setVal("联通", $liant, counttime_liant, count_liant);
    this.setVal("移动", $yid, counttime_yid, count_yid);
    if (this.type == "ping") {
        $haiw.sort(this.SortDesc);
        this.setVal("海外", $haiw, counttime_haiw, count_haiw);
    }
    this.setFastSlowAvg($newall, counttime_all, count_all);
};
//全部线路最快最慢平均值赋值操作
Pings.prototype.setFastSlowAvg = function ($newall, counttime_all, count_all) {
    if ($newall.length == 0) return;
    var $first = $($newall[0]);
    var $last = $($newall[$newall.length - 1]);
    var firstServer = this.FormatHtml($first.find("[name=city]").html());
    var lastServer = this.FormatHtml($last.find("[name=city]").html());
    if (this.type == "ping") {
        $("#fast").html('<span>最快</span>{0} <strong class="col-green02">{1}</strong>'.format(firstServer, this.FormatHtml($first.find("[name=responsetime]").html().replace(/\[\S+\]/, ''))));
        $("#slow").html('<span>最慢</span>{0} <strong class="col-hint">{1}</strong>'.format(lastServer, this.FormatHtml($last.find("[name=responsetime]").html().replace(/\[\S+\]/, ''))));
        $("#avg").html('<span>平均</span><strong class="col-blue02">{0}</strong>'.format(this.division(counttime_all, count_all) + "ms"));
    } else if (this.type == "speedtest") {
        var $p = $("#ulsumary tr:eq(1) td:eq(1).sp");
        $p.find("span:eq(0)").html(firstServer);
        $p.find("span:eq(1)").html(this.FormatHtml($first.find("[name=alltime]").html().replace(/\[\S+\]/, '')));
        $p.eq(1).html($first.find("[name=support]").html());
    } else if (this.type == "speedcom") {
        var fastid, slowid, avgid;
        if ($($newall[0]).attr("flag") == "test") {
            fastid = "fast";
            slowid = "slow";
            avgid = "avg";
        } else {
            fastid = "fast1";
            slowid = "slow1";
            avgid = "avg1";
        }
        try {
            $("#" + fastid).html('<div class="curtsub bor-l1s02">最快：{0}<strong class="col-green02">{1}</strong></div>'.format(this.FormatHtml($first.parents(".row").find("[name=city]").html()), this.FormatHtml($first.find("[name=alltime]").html().replace(/\[\S+\]/, ''))));
            $("#" + slowid).html('<div class="curtsub">最慢：{0}<strong class="col-green02">{1}</strong></div>'.format(this.FormatHtml($last.parents(".row").find("[name=city]").html()), this.FormatHtml($last.find("[name=alltime]").html().replace(/\[\S+\]/, ''))));
            $("#" + avgid).html('<div class="curtsub">平均：<strong class="col-blue02">{0}</strong></div>'.format(this.division(counttime_all, count_all) + "ms"));

        } catch (e) {
            var a = 0;
        }
    }
};
//线路最快最慢平均值赋值操作
Pings.prototype.setVal = function (linetype, obj, counttime, count) {
    if (obj.length) {
        var $li = $("#ulsumary tr:contains('" + linetype + "')");
        if (this.type == "ping") {
            $li.find("[name=fast] span:eq(0)").html(this.FormatHtml($(obj[0]).find("[name=city]").html().replace(/\[\S+\]/, '')));
            $li.find("[name=fast] span:eq(1)").html(this.FormatHtml($(obj[0]).find("[name=responsetime]").html().replace(/\[\S+\]/, '')));
            $li.find("[name=slow] span:eq(0)").html(this.FormatHtml($(obj[obj.length - 1]).find("[name=city]").html().replace(/\[\S+\]/, '')));
            $li.find("[name=slow] span:eq(1)").html(this.FormatHtml($(obj[obj.length - 1]).find("[name=responsetime]").html().replace(/\[\S+\]/, '')));
            $li.find("[name=time]").html(this.division(counttime, count) + "ms");
        } else if (this.type == "speedcom") {
            $li.find("[name=fast] span:eq(0)").html(this.FormatHtml($(obj[0]).parents("li").find("[name=city]").html().replace(/\[\S+\]/, '')));
            $li.find("[name=fast] span:eq(1)").html(this.FormatHtml($(obj[0]).find("[name=alltime]").html().replace(/\[\S+\]/, '')));
            $li.find("[name=support]").html($(obj[0]).parents("li").find("[name=support]").html());

            $li.find("[name=fast1] span:eq(0)").html(this.FormatHtml($(obj[0]).parents("li").find("[name=city]").html().replace(/\[\S+\]/, '')));
            $li.find("[name=fast1] span:eq(1)").html(this.FormatHtml($(obj[0]).find("[name=alltime]").html().replace(/\[\S+\]/, '')));
            $li.find("[name=support1]").html($(obj[0]).parents("li").find("[name=support]").html());
        } else {
            $li.find("[name=fast] span:eq(0)").html(this.FormatHtml($(obj[0]).find("[name=city]").html().replace(/\[\S+\]/, '')));
            $li.find("[name=fast] span:eq(1)").html(this.FormatHtml($(obj[0]).find("[name=alltime]").html().replace(/\[\S+\]/, '')));
            $li.find("[name=support]").html($(obj[0]).find("[name=support]").html());
        }
    }
};
//海外节点国家/地区最快节点
Pings.prototype.bindCountryFastSlow = function () {
    var $all = $("#speedlist div.row[state=2]");
    var $chk = [], $usa = [], $uk = [], $kor = [], $net = [], $jap = [], $newall = [];
    var counttime_all = 0;
    var count_all = 0;
    for (var i = 0; i < $all.length; i++) {
        var $li = $($all[i]);
        var time = 9999;
        if (parseInt(this.ishost))
            time = this.FormatInt($li.find("[name=\"alltime\"]").text());
        else
            time = this.FormatInt($li.find("[name=\"responsetime\"]").text());
        if (time == 9999)
            continue;
        $newall.push($li);
        counttime_all += time;
        count_all++;
        var txt = $li.find("span[name=city]").text();
        if (txt.indexOf("中国香港") >= 0) {
            $chk.push($li);
        } else if (txt.indexOf("美国") >= 0) {
            $usa.push($li);
        } else if (txt.indexOf("英国") >= 0) {
            $uk.push($li);
        } else if (txt.indexOf("韩国") >= 0) {
            $kor.push($li);
        } else if (txt.indexOf("荷兰") >= 0) {
            $net.push($li);
        } else if (txt.indexOf("日本") >= 0) {
            $jap.push($li);
        }
    }
    $newall.sort(this.SortDesc);
    $chk.length ? $chk.sort(this.SortDesc) : $chk;
    $usa.length ? $usa.sort(this.SortDesc) : $usa;
    $uk.length ? $uk.sort(this.SortDesc) : $uk;
    $kor.length ? $kor.sort(this.SortDesc) : $kor;
    $net.length ? $net.sort(this.SortDesc) : $net;
    $jap.length ? $jap.sort(this.SortDesc) : $jap;
    this.setVal("中国香港", $chk);
    this.setVal("美国", $usa);
    this.setVal("英国", $uk);
    this.setVal("韩国", $kor);
    this.setVal("荷兰", $net);
    this.setVal("日本", $jap);
};
//Ajax返回speedtest结果处理方法
Pings.prototype.speedTest = function (li, msg) {
    li.find("[name=ip]").html("<a href='http://ip.chinaz.com/?ip=" + msg.result.ip + "' target='_blank' title='" + msg.result.ipaddress + "'>" + msg.result.ip + "</a>");
    li.find("[name=httpstate]").html(msg.result.httpstate);
    this.setTimeStyle(msg.result.alltime, "alltime", li);
    this.setTimeStyle(msg.result.dnstime, "dnstime", li);
    this.setTimeStyle(msg.result.conntime, "conntime", li);
    this.setTimeStyle(msg.result.downtime, "downtime", li);
    li.find("[name=filesize]").html(msg.result.filesize + "KB");
    li.find("[name=downspeed]").html(msg.result.downspeed + "KB");
    var cdiv;
    if (this.type == "speedtest" || this.type == "speedworld")
        cdiv = li.find("div:eq(-2)");
    else if (this.type == "speedcom")
        cdiv = li.find("dd:eq(-1)");
    cdiv.find("a:eq(0)").attr("href", "http://ping.chinaz.com/?host=" + msg.result.ip);
    cdiv.find("a:eq(1)").attr("href", "/tracert?ip=" + $("#hostd").val() + "&se=" + cdiv.find("a:eq(1)").attr("se"));
    if (this.xyiplist.length) {
        if (this.xyiplist.toString().indexOf(msg.result.ip) < 0)
            this.xyiplist.push(msg.result.ip);
    } else
        this.xyiplist.push(msg.result.ip);
    li.find("[name=ip]").attr("ipgroup", this.xyiplist.toString().indexOf(msg.result.ip));
    var sc, guid;
    if (this.type == "speedcom") {
        sc = li.parents(".row").find("span[name=city]").html();
        guid = li.parents(".row").attr("id");

    } else {
        sc = li.find("[name=city]").html();
        guid = li.attr("id");
    }
    //if (this.chart) {
    var sd = {};
    if (this.type == "speedworld") {
        //sd = this.getSeriesData_world(sc, msg.result.alltime.replace("毫秒", ""), this.seriesData, this.ECHARTS, guid);
        //this.ECHARTS.bindChart(sd, this.chart);
    }
    else {
        if (this.type == "speedcom") {
            //网站测速对比时  用两组seriersData分别存放相关数据
            if (li.attr("flag") == "test") {
                sd = this.getSeriesData(sc, msg.result.alltime.replace("毫秒", ""), this.seriesData, this.seriesData1, this.seriesData2, this.seriesData3, this.ECHARTS, guid);
            }
            else {
                sd = this.getSeriesData(sc, msg.result.alltime.replace("毫秒", ""), this._seriesData, this._seriesData1, this._seriesData2, this._seriesData3, this._ECHARTS, guid, true);
            }
        } else {
            sd = this.getSeriesData(sc, msg.result.alltime.replace("毫秒", ""), this.seriesData, this.seriesData1, this.seriesData2, this.seriesData3, this.ECHARTS, guid);
        }
    }
    //}
};
//大于5000ms为红色字体
Pings.prototype.setTimeStyle = function (time, nameVal, obj) {
    if (this.FormatInt(time) >= 5000)
        obj.find("[name=" + nameVal + "]").html("<font color=red>" + time + "ms</font>");
    else
        obj.find("[name=" + nameVal + "]").html(time + "ms");
};
//超时
Pings.prototype.timeOut = function (li) {
    if (parseInt(li.attr("trycount")) >= 2) {
        li.attr("state", 2);
        li.find("[name=ip]").html('<font color=red>超时(<a href="javascript:" class="retry">重试</a>)</font>');
        var sc, guid;
        if (this.type == "speedcom") {
            sc = li.parents(".row").find("[name=city]").html();
            guid = li.parents(".row").attr("id");
        }
        else {
            sc = li.find("[name=city]").html();
            guid = li.attr("id");
        }
        this.cnt++;
        li.find("[name=ip]").attr("ipgroup", "-1");
        $("#gjd").text($("div.row[state=\"2\"]").length);
        if (!this.isertry) {
            //if (this.chart) {
            var sd = {};
            if (this.type == "speedworld") {
                // sd = this.getSeriesData_world(sc, "超时", this.seriesData, this.ECHARTS, guid);
                //this.ECHARTS.bindChart(sd, this.chart);
            }
            else {
                if (this.type == "speedcom") {
                    //网站测速对比时  用两组seriersData分别存放相关数据
                    if (li.attr("flag") == "test") {
                        sd = this.getSeriesData(sc, "超时", this.seriesData, this.seriesData1, this.seriesData2, this.seriesData3, this.ECHARTS, guid);
                    }
                    else {
                        sd = this.getSeriesData(sc, "超时", this._seriesData, this._seriesData1, this._seriesData2, this._seriesData3, this._ECHARTS, guid, true);
                    }
                } else {
                    sd = this.getSeriesData(sc, "超时", this.seriesData, this.seriesData1, this.seriesData2, this.seriesData3, this.ECHARTS, guid);
                }
            }
            //}
        }
    }
    else {
        li.attr("state", 0);
        var trycount = parseInt(li.attr("trycount"));
        li.attr("trycount", trycount += 1);
    }
};
//点击重试，重新获取对应seriesData
Pings.prototype.seriesDataInit = function (obj) {
    var linetype = $(obj).parents("li").attr("linetype");
    var guid = $(obj).parents("li").attr("id");
    switch (linetype) {
        case "1":
        case "5":
            if (this.type == "speedcom") {
                if ($(obj).parents("dl").attr("flag") == "test")
                    this.seriesData = this.eachDelSeriesData(this.seriesData, guid);
                else
                    this._seriesData = this.eachDelSeriesData(this._seriesData, guid);
            }
            else {
                if (this.type == "speedworld") this.ECHARTS.toolWorldFormat = this.eachDelSeriesData(this.ECHARTS.toolWorldFormat, guid);
                this.seriesData = this.eachDelSeriesData(this.seriesData, guid);
            }
            break;
        case "2":
            if (this.type == "speedcom") {
                if ($(obj).parents("dl").attr("flag") == "test")
                    this.seriesData1 = this.eachDelSeriesData(this.seriesData1, guid);
                else
                    this._seriesData1 = this.eachDelSeriesData(this._seriesData1, guid);
            }
            else
                this.seriesData1 = this.eachDelSeriesData(this.seriesData1, guid);
            break;
        case "3":
            if (this.type == "speedcom") {
                if ($(obj).parents("dl").attr("flag") == "test")
                    this.seriesData2 = this.eachDelSeriesData(this.seriesData2, guid);
                else
                    this._seriesData2 = this.eachDelSeriesData(this._seriesData2, guid);
            }
            else
                this.seriesData2 = this.eachDelSeriesData(this.seriesData2, guid);
            break;
        case "4":
            if (this.type == "speedcom") {
                if ($(obj).parents("dl").attr("flag") == "test")
                    this.seriesData3 = this.eachDelSeriesData(this.seriesData3, guid);
                else
                    this._seriesData3 = this.eachDelSeriesData(this._seriesData3, guid);
            }
            else
                this.seriesData3 = this.eachDelSeriesData(this.seriesData3, guid);
            break;
    }
};
//删除对应seriesData中已有的项
Pings.prototype.eachDelSeriesData = function (seriesData, guid) {
    for (var i = 0; i < seriesData.length; i++) {
        var nguid = seriesData[i].guid;
        if (nguid == guid) {
            seriesData.splice(i, 1);
            break;
        }
    }
    return seriesData;
};
//Ajax
Pings.prototype.ajaxPing = function (li, o) {
    var _this = this;
    (function (li) {
        var guid, host;
        if (_this.type == "speedcom") {
            guid = li.parents(".row").attr("id"); //guid
            host = $(li).find("dd a.overhid").text();
        } else {
            guid = li.attr("id");
            host = _this.host;
        }
        jQuery.ajax({
            type: "POST",
            url: "/iframe.ashx?t=ping",
            dataType: 'jsonp',
            data: "guid=" + guid + "&host=" + host + "&ishost=" + _this.ishost + "&encode=" + _this.enkey + "&checktype=" + _this.checktype,
            beforeSend: function () {
                li.find("[name=ip]").html(_this.loadimg);
                if (_this.type == "speedcom") {
                    li.find("[name=ip] img").addClass("fl");
                }
                $("i.sort").css("display", "none");
                $(".showb").hide();
                li.attr("state", 1);
            },
            complete: function (XMLHttpRequest, textStatus) {
                if (textStatus != 'success') {
                    li.attr("state", 2);
                    li.find("[name=ip]").html('<font color=red>超时(<a href="javascript:" class="retry">重试</a>)</font>');
                    if (!_this.isertry)//重试则不重新提交ajax
                        _this.start();
                }
            },
            success: function (msg) {
                var complage, complage_test, complage_test1; //执行成功 存起来
                if (msg.state == 1) {
                    li.attr("state", 2);
                    if (parseInt(_this.ishost)) _this.speedTest(li, msg);
                    else _this.ping(li, msg);
                } else _this.timeOut(li); //超时
                if (_this.type == "ping") {
                    _this.list = $("#speedlist div.listw[state]");
                    complage = $("#speedlist div.listw[state=2]");
                } else if (_this.type == "speedcom") {
                    _this.list = $("#speedlist div.listw dl");
                    if (li.attr("flag") == "test") complage = $("#speedlist div.row dl[state=2][flag=test]");
                    else complage = $("#speedlist div.listw dl[state=2][flag=test1]");
                } else {
                    _this.list = $("#speedlist div.listw[state]");
                    complage = $("#speedlist div.listw[state=2]");
                }
                //非IE低版本时，实时加载
                if (!sys.ie || sys.ie > 8) {
                    if (_this.type == "speedworld") _this.bindCountryFastSlow();
                    else if (_this.type == "speedcom") {
                        if (li.attr("flag") == "test") _this.bindFastSlowAvg(complage);
                        else _this.bindFastSlowAvg(complage);
                    }
                    else _this.bindFastSlowAvg(complage);
                }
                //重试则不重新提交ajax
                if (!_this.isertry) _this.start();
                _this.isertry = 0;
                //全部执行完成 
                if (_this.type == "speedcom") {
                    if (_this.list.length == $("#speedlist div.row dl[state=2]").length) _this.ajaxCompleteAll(complage);
                } else {
                    if (_this.list.length == complage.length) _this.ajaxCompleteAll(complage);
                }
            }
        });
    })(li);
};
//Ajax全部执行完成
Pings.prototype.ajaxCompleteAll = function (complage) {
    $("i.sort").css("display", "inline-block");
    $(".showb").css("display", "inline-block");
    if ($("a.retry:visible").length > 0) {
        $(".allRetry").show();
    }
    if (this.type == "ping") {
        this.areaAvgflag = false; //修改绑定地区平均值为不绑定
        this.ping.getSelectBindAreaAvg(0, this);
    }
    if (sys.ie && sys.ie < 9) {
        if (this.type == "speedworld") this.bindCountryFastSlow();
        else if (this.type == "speedcom") {
            this.bindFastSlowAvg($("#speedlist div.row dl[state=2][flag=test]"));
            this.bindFastSlowAvg($("#speedlist div.row dl[state=2][flag=test1]"));
        } else this.bindFastSlowAvg(complage);
    }
};
//获取地图数据(国内)
Pings.prototype.getSeriesData = function (sc, alltime, seriesData, seriesData1, seriesData2, seriesData3, ECHARTS, guid, is2) {
    /*for (var p = 0; p < ECHARTS.provinces.length; p++) {
        var pname = ECHARTS.provinces[p];
        alltime = alltime == "<1" ? "0.5" : alltime;
        var sd = { name: pname, value: (alltime.indexOf("超时") >= 0 ? 0 : alltime), type: sc, guid: guid };
        if (sc.indexOf("电信") > 0 && sc.indexOf(pname) >= 0) {
            seriesData.push(sd);
            break;
        } else if (sc.indexOf("多线") > 0 && sc.indexOf(pname) >= 0) {
            seriesData1.push(sd);
            break;
        } else if (sc.indexOf("联通") > 0 && sc.indexOf(pname) >= 0) {
            seriesData2.push(sd);
            break;
        } else if (sc.indexOf("移动") > 0 && sc.indexOf(pname) >= 0) {
            seriesData3.push(sd);
            break;
        }
    }*/
    /*
    { start: 0.1, end: 50, label: '<=50' },
            { start: 50.1, end: 100, label: '50~100' },
            { start: 100.1, end: 150, label: '100~150' },
            { start: 150.1, end: 200, label: '150~200' },
            { start: 200.1, label: '>200' },
            { end: 0, label: '超时' }
            ];
    */
    for (var item in svgmapchina.mapdata) {
        var pname = svgmapchina.mapdata[item].areas;
        if (pname.indexOf(' ') > 0) {
            pname = pname.replace(' ', '');
        }
        alltime = alltime == "<1" ? "0.5" : alltime;
        var sd = { name: pname, value: (alltime.indexOf("超时") >= 0 ? 0 : alltime), type: sc, guid: guid };
        //if (!mapdata[item].avgValue) mapdata[item].avgValue = [];
        if (sc.indexOf("电信") > 0 && sc.indexOf(pname) >= 0) {
            seriesData.push(sd);
            //if (is2) {
            //    if (sd.value > 0)
            //        mapdata1[item].serverCount = (mapdata1[item].serverCount | 0) + 1;
            //    mapdata1[item].value = ((mapdata1[item].value | 0) + sd.value * 1);
            //    mapdata1[item].data.push(sd);
            //    obj_contents.pathElms[item].avgValue = mapdata1[item].value / mapdata1[item].serverCount;
            //    obj_contents.pathElms[item].elm.stop().animate({ fill: getItemBg(mapdata1[item].value / mapdata1[item].serverCount) }, 200);
            //} else {
            //    if (sd.value > 0)
            //        mapdata[item].serverCount = (mapdata[item].serverCount | 0) + 1;
            //    mapdata[item].value = ((mapdata[item].value | 0) + sd.value * 1);
            //    mapdata[item].data.push(sd);
            //    obj_contents.pathElms[item].avgValue = mapdata[item].value / mapdata[item].serverCount;
            //    obj_contents.pathElms[item].elm.stop().animate({ fill: getItemBg(mapdata[item].value / mapdata[item].serverCount) }, 200);
            //}
            if (is2)
                svgmapchina1.bindMapdata(sd, item);
            else
                svgmapchina.bindMapdata(sd, item);
            //break;
        } else if (sc.indexOf("多线") > 0 && sc.indexOf(pname) >= 0) {
            seriesData1.push(sd);
            if (is2)
                svgmapchina1.bindMapdata(sd, item);
            else
                svgmapchina.bindMapdata(sd, item);
            // break;
        } else if (sc.indexOf("联通") > 0 && sc.indexOf(pname) >= 0) {
            seriesData2.push(sd);
            if (is2)
                svgmapchina1.bindMapdata(sd, item);
            else
                svgmapchina.bindMapdata(sd, item);
            // break;
        } else if (sc.indexOf("移动") > 0 && sc.indexOf(pname) >= 0) {
            seriesData3.push(sd);
            if (is2)
                svgmapchina1.bindMapdata(sd, item);
            else
                svgmapchina.bindMapdata(sd, item);
            // break;
        }
    }
    return { seriesData: seriesData, seriesData1: seriesData1, seriesData2: seriesData2, seriesData3: seriesData3 };
};
SVG_Map_China.prototype.bindMapdata = function (sd, item) {
    if (sd.value > 0)
        this.mapdata[item].serverCount = (this.mapdata[item].serverCount | 0) + 1;
    else
        this.mapdata[item].serverCount = (this.mapdata[item].serverCount | 0);
    this.mapdata[item].value = ((this.mapdata[item].value | 0) + sd.value * 1);
    for (var i = 0; i < this.mapdata[item].data.length; i++) {
        var d = this.mapdata[item].data[i];
        if (d.guid == sd.guid) {
            this.mapdata[item].data[i] = '';
            break;
        }
    }
    this.mapdata[item].data = this.mapdata[item].data.trimArray();
    this.mapdata[item].data.push(sd);
    this.obj_contents.pathElms[item].avgValue = this.mapdata[item].value > 0 ? (this.mapdata[item].value / this.mapdata[item].serverCount) : 0;
    this.obj_contents.pathElms[item].elm.stop().animate({ fill: getItemBg(this.mapdata[item].value > 0 ? (this.mapdata[item].value / this.mapdata[item].serverCount) : 0) }, 200);
};
function bindMapdata(sd, item, is2) {
    if (is2) {

    } else {
        if (sd.value > 0)
            svgmapchina.mapdata[item].serverCount = (svgmapchina.mapdata[item].serverCount | 0) + 1;
        svgmapchina.mapdata[item].value = ((svgmapchina.mapdata[item].value | 0) + sd.value * 1);
        svgmapchina.mapdata[item].data.push(sd);
        svgmapchina.obj_contents.pathElms[item].avgValue = svgmapchina.mapdata[item].value / svgmapchina.mapdata[item].serverCount;
        svgmapchina.obj_contents.pathElms[item].elm.stop().animate({ fill: getItemBg(svgmapchina.mapdata[item].value / svgmapchina.mapdata[item].serverCount) }, 200);
    }
};
function getItemBg(v) {
    if ($("#ftype").val() == "ping") {
        if (v > 0 && v <= 50)
            return "#24AA1D";
        else if (v > 50 && v <= 100)
            return "#42DD3F";
        else if (v > 100 && v <= 150)
            return "#BEF663";
        else if (v > 150 && v <= 200)
            return "#F6ED44";
        else if (v > 200)
            return "#F69833";
        else if (v == 0)
            return "#E61610";
        else
            return "#FFF";
    } else {
        if (v > 0 && v <= 400)
            return "#24AA1D";
        else if (v > 400 && v <= 800)
            return "#42DD3F";
        else if (v > 800 && v <= 1200)
            return "#BEF663";
        else if (v > 1200 && v <= 1600)
            return "#F6ED44";
        else if (v > 1600)
            return "#F69833";
        else if (v == 0)
            return "#E61610";
        else
            return "#FFF";
    }
}
//获取地图数据(海外)
Pings.prototype.getSeriesData_world = function (sc, alltime, seriesData, ECHARTS, guid) {
    for (var c = 0; c < ECHARTS.country.length; c++) {
        var o_country = ECHARTS.country[c];
        if (sc.indexOf('香港') >= 0 || sc.indexOf('澳门') >= 0)
            o_country = "中国";
        var sd = { name: o_country, value: (alltime.indexOf("超时") >= 0 ? 0 : alltime), type: sc, guid: guid };
        if (sc.indexOf(o_country) >= 0 || (o_country == '中国' && (sc.indexOf('香港') >= 0 || sc.indexOf('澳门') >= 0))) {
            seriesData.push(sd);
            ECHARTS.toolWorldFormat.push({ "guid": guid, "sc": sc + '-' + sd.value });
            break;
        }
    }
    return { seriesData: seriesData };
};
//分析节点类型
Pings.prototype.getLinetype = function (n) {
    switch (n) {
        case "1": return "电信";
        case "2": return "多线";
        case "3": return "联通";
        case "4": return "移动";
        case "5": return "海外";
    }
    return "";
};
//格式化数据相关
Pings.prototype.division = function (x, y) {
    if (y == 0)
        return 0;
    return (x / y).toString().indexOf('.') > 0 ? (x / y).toFixed(1) : (x / y);
};
Pings.prototype.FormatInt = function (x) {
    var xint = 9999;
    x = x.replace('毫秒', '').replace('ms', '').replace('-', '');
    if (x == '' || x.indexOf('超时') != -1)
        return xint;
    if (x == '<1')
        return 0;
    return parseInt(x);
};
Pings.prototype.FormatDouble = function (x) {
    var xint = 9999;
    x = x.replace('毫秒', '').replace('ms', '').replace('-', '');
    if (x == '' || x.indexOf('超时') != -1)
        return xint;
    if (x == '<1')
        return 0;
    return x.toString().indexOf('.') > 0 ? parseFloat(x).toFixed(1) : x;
};
Pings.prototype.FormatHtml = function (x) {
    if (x)
        return x.replace(/<[^>]*>|<\/[^>]*>/gm, "");
};
//倒序排序
Pings.prototype.SortDesc = function (x, y) {
    var xint;
    var yint;
    if ($("#ishost").val() == "0") {
        var r = _pings.SortDescAscPing(x, y);
        xint = r.xint;
        yint = r.yint;
    }
    else {
        var r = _pings.SortDescAscSpeedTest(x, y);
        xint = r.xint;
        yint = r.yint;
    }
    if (xint > yint)
        return 1;
    else if (xint < yint)
        return -1;
    else
        return 0;

};
//顺序排序
Pings.prototype.SortAsc = function (x, y) {
    var xint;
    var yint;
    if ($("#ishost").val() == "0") {
        var r = _pings.SortDescAscPing(x, y);
        xint = r.xint;
        yint = r.yint;
    }
    else {
        var r = _pings.SortDescAscSpeedTest(x, y);
        xint = r.xint;
        yint = r.yint;
    }
    if (xint > yint)
        return -1;
    else if (xint < yint)
        return 1;
    else
        return 0;

};
//Ping排序公共方法
Pings.prototype.SortDescAscPing = function (x, y) {
    var xint;
    var yint;
    switch (_pings.sorttype) {
        case 0: //监测点
            var serveruroupx = $(x).find("[name=city]").attr("serveruroup");
            var serveruroupy = $(y).find("[name=city]").attr("serveruroup");
            xint = parseInt(serveruroupx ? serveruroupx.trim() : -1); yint = parseInt(serveruroupy ? serveruroupy.trim() : -1);
            break;
        case 1: //ip
            var ipgroupx = $(x).find("[name=ip]").attr("ipgroup");
            var ipgroupy = $(y).find("[name=ip]").attr("ipgroup");
            xint = parseInt(ipgroupx ? ipgroupx.trim() : -1); yint = parseInt(ipgroupy ? ipgroupy.trim() : -1);
            break;
        case 2: //响应时间
            var responsetimex = _pings.FormatInt($(x).find("[name=\"responsetime\"]").text());
            var responsetimey = _pings.FormatInt($(y).find("[name=\"responsetime\"]").text());
            xint = parseInt(responsetimex ? responsetimex : -1); yint = parseInt(responsetimey ? responsetimey : -1);
            break;
        case 3: //地区（省份）节点
            var timex = $(x).find("span:eq(1)").text();
            if (timex == "-")
                timex = "9999";
            else if (timex == "<1ms")
                timex = "0";
            timex = timex.replace("ms", "");
            var timey = $(y).find("span").text();
            if (timey == "-")
                timey = "9999";
            else if (timey == "<1ms")
                timey = "0";
            timey = timey.replace("ms", "");
            xint = parseFloat(timex ? timex.trim() : -1); yint = parseFloat(timey ? timey.trim() : -1);
            break;
        default: //响应时间(默认)
            var responsetimex = _pings.FormatInt($(x).find("[name=\"responsetime\"]").text());
            var responsetimey = _pings.FormatInt($(y).find("[name=\"responsetime\"]").text());
            xint = parseInt(responsetimex ? responsetimex : -1); yint = parseInt(responsetimey ? responsetimey : -1);
            break;
    }
    return { xint: xint, yint: yint };
};
//speedtest排序公共方法
Pings.prototype.SortDescAscSpeedTest = function (x, y) {
    var xint;
    var yint;
    switch (_pings.sorttype) {
        case 0: //总耗时
            var alltimex = _pings.FormatInt($(x).find("[name=\"alltime\"]").text());
            var alltimey = _pings.FormatInt($(y).find("[name=\"alltime\"]").text());
            xint = parseInt(alltimex ? alltimex : -1); yint = parseInt(alltimey ? alltimey : -1);
            break;
        case 1: //解析时间
            var dnstimex = _pings.FormatInt($(x).find("[name=\"dnstime\"]").text());
            var dnstimey = _pings.FormatInt($(y).find("[name=\"dnstime\"]").text());
            xint = parseInt(dnstimex ? dnstimex : -1); yint = parseInt(dnstimey ? dnstimey : -1);
            break;
        case 2: //连接时间
            var conntimex = _pings.FormatInt($(x).find("[name=\"conntime\"]").text());
            var conntimey = _pings.FormatInt($(y).find("[name=\"conntime\"]").text());
            xint = parseInt(conntimex ? conntimex : -1); yint = parseInt(conntimey ? conntimey : -1);
            break;
        case 3: //下载时间
            var downtimex = _pings.FormatInt($(x).find("[name=\"downtime\"]").text());
            var downtimey = _pings.FormatInt($(y).find("[name=\"downtime\"]").text());
            xint = parseInt(downtimex ? downtimex : -1); yint = parseInt(downtimey ? downtimey : -1);
            break;
        case 4: //下载速度
            var downspeedx = _pings.FormatInt($(x).find("[name=\"downspeed\"]").text());
            var downspeedy = _pings.FormatInt($(y).find("[name=\"downspeed\"]").text());
            xint = parseInt(downspeedx ? downspeedx : -1); yint = parseInt(downspeedy ? downspeedy : -1);
            break;
        case 5: //监测点
            var serveruroupx = $(x).find("[name=city]").attr("serveruroup");
            var serveruroupy = $(y).find("[name=city]").attr("serveruroup");
            xint = parseInt(serveruroupx ? serveruroupx.trim() : -1); yint = parseInt(serveruroupy ? serveruroupy.trim() : -1);
            break;
        case 6: //ip
            var ipgroupx = $(x).find("[name=ip]").attr("ipgroup");
            var ipgroupy = $(y).find("[name=ip]").attr("ipgroup");
            xint = parseInt(ipgroupx ? ipgroupx.trim() : -1); yint = parseInt(ipgroupy ? ipgroupy.trim() : -1);
            break;
        default: //总耗时(默认)
            var alltimex = _pings.FormatInt($(x).find("[name=\"alltime\"]").text());
            var alltimey = _pings.FormatInt($(y).find("[name=\"alltime\"]").text());
            xint = parseInt(alltimex ? alltimex : -1); yint = parseInt(alltimey ? alltimey : -1);
            break;
    }
    return { xint: xint, yint: yint };
};
//Ping的排序事件调用函数
Pings.prototype.PingSort = function (obj) {
    var s = jQuery(obj).attr('s');
    var st = jQuery(obj).attr('st');
    _pings.sorttype = parseInt(st);
    var _this = this;
    var list;
    if (s == null)
        s = 'desc';
    jQuery("i[st]").removeAttr("s").removeClass("up down");
    if (s == 'desc') list = this.getSortList(this.SortDesc, st, "asc", "down", obj);
    else list = this.getSortList(this.SortAsc, st, "desc", "up", obj);
    var html = this.getSortData(list, _this.type);
    jQuery("#speedlist div:first").siblings().remove();
    jQuery("#speedlist div:first").after(html);
};
Pings.prototype.getSortList = function (callSort, st, descasc, updown, obj) {
    var list;
    if (this.type == "speedcom") {
        if (_pings.sorttype == 5) {
            list = this.list.parents("div.listw").sort(callSort);
            jQuery("i[st=" + st + "]").attr('s', descasc).removeClass().addClass("sort cursor " + updown);
        }
        else {
            var _thisshowb = jQuery(obj).parents(".pa").siblings(".showb");
            $(".sortlist .showb").not(_thisshowb).removeClass("showb15");
            _thisshowb.removeClass("showb9").addClass("showb15");
            if (this.sortFlag == "test") {
                list = this.list.parent().find("[flag=test]").sort(callSort);
                jQuery("i[st=" + st + "][flag=test]").attr('s', descasc).removeClass().addClass("sort cursor " + updown);
            }
            else {
                list = this.list.parent().find("[flag=test1]").sort(callSort);
                jQuery("i[st=" + st + "][flag=test1]").attr('s', descasc).removeClass().addClass("sort cursor " + updown);
            }
        }
    }
    else {
        list = this.list.sort(callSort);
        jQuery("i[st=" + st + "]").attr('s', descasc).removeClass().addClass("sort cursor " + updown);
    }
    return list;
};
//返回排序后的HTML
Pings.prototype.getSortData = function (list, type) {
    var html = "";
    jQuery.each(list, function (i, n) {
        var li = $(n);
        var _class = "row listw tc clearfix";
        if (type == "speedworld") {
            _class = "row listw  clearfix";
        }
        if (type == "speedcom") {
            _class = "row listw tc clearfix compare";
            if (_pings.sorttype == 5)
                html += '<div id="{0}" class="{1}" linetype="{2}">{3}</div>'.format(li.attr("id"), _class, li.attr("linetype"), $(n).html());
            else
                html += '<div id="{0}" class="{1}" linetype="{2}">{3}</div>'.format(li.parents(".listw").attr("id"), _class, li.parents(".listw").attr("linetype"), $(n).parents(".listw").html());
        }
        else
            html += '<div id="{0}" class="{1}" linetype="{2}" state="{3}" trycount="{4}">{5}</div>'.format(li.attr("id"), _class, li.attr("linetype"), li.attr("state"), li.attr("trycount"), $(n).html());
    });
    return html;
};
//Ping地区排序
ping_fn.PingSortArea = function (list, obj) {
    //_pings.sorttype = 3;
    //list.sort(this._this.SortDesc);
    list.sort(function (x, y) {
        var xint;
        var yint;
        if ($("#ishost").val() == "0") {
            var timex = $(x).find("span:eq(1)").text();
            if (timex == "-")
                timex = "9999";
            else if (timex == "<1ms")
                timex = "0";
            timex = timex.replace("ms", "");
            var timey = $(y).find("span:eq(1)").text();
            if (timey == "-")
                timey = "9999";
            else if (timey == "<1ms")
                timey = "0";
            timey = timey.replace("ms", "");
            xint = parseFloat(timex ? timex.trim() : -1); yint = parseFloat(timey ? timey.trim() : -1);
        }
        else {
            var downtimex = _pings.FormatInt($(x).find("[name=\"downtime\"]").text());
            var downtimey = _pings.FormatInt($(y).find("[name=\"downtime\"]").text());
            xint = parseInt(downtimex ? downtimex : -1); yint = parseInt(downtimey ? downtimey : -1);
        }
        if (xint > yint)
            return 1;
        else if (xint < yint)
            return -1;
        else
            return 0;

    });

    var cnt = 1;
    var pstr = "";
    var _this = this;
    list.each(function () {
        var time = $(this).find("span:eq(1)").text();
        if (time == "<1ms") {
            time = _pings.FormatDouble("0.5") == 9999 ? 0 : _pings.FormatDouble("0.5");
        } else
            time = _pings.FormatInt(time) == 9999 ? 0 : _pings.FormatInt(time);
        var col = "";
        if (parseFloat(time) >= 0.1 && parseFloat(time) <= 100)
            $(this).find("span:eq(1)").attr("class", "col-green");
        else if (parseFloat(time) >= 101 && parseFloat(time) <= 200)
            $(this).find("span:eq(1)").attr("class", "col-hint02");
        else
            $(this).find("span:eq(1)").attr("class", "col-hint");

        if (cnt % 4 == 0)
            pstr += '<td class="sp">' + $(this).html() + '</td>';
        else
            pstr += '<td class="sp">' + $(this).html() + '</span></td>';
        if (cnt % 4 == 0 && cnt < list.length)
            pstr += '</tr><tr class="list-cont">';
        cnt++;
    });
    if (pstr != "") pstr = "<tr class='list-cont'>" + pstr;
    obj.html(pstr);
};
//显示/隐藏列表中相关ip节点信息
ping_fn.toggleIP = function (ip, obj) {
    if ($(obj).attr("chk") == 0) {
        $("#speedlist div.row").show();
        $(obj).attr("chk", "1").css("color", "#338de6");
    } else {
        //var nlist = $("#speedlist .PingRLlist");
        $(obj).attr("chk", "0").css("color", "#ff0000");
        $("#speedlist div.listw").each(function () {
            var nip = $(this).find("div[name=ip]").text();
            if (nip != ip)
                $(this).hide();
            else
                $(this).show();
        });
    }
};

/***************实现地图*****************/
function Echarts(chartid, title) {
    this.chartid = chartid;
    this.type;
    this.isertry = 0;
    this.nullserver = []; //无检测点的省份/地区配置信息
    this.nuprovince = []; //无检测点的省份/地区
    this.provinces = ["北京", "天津", "河北", "山西", "内蒙古", "上海", "山东", "江苏", "浙江", "江西", "安徽", "福建", "台湾", "湖北", "湖南", "河南", "广东", "广西", "海南", "香港", "澳门", "重庆", "四川", "贵州", "云南", "西藏", "陕西", "甘肃", "宁夏", "新疆", "青海", "黑龙江", "吉林", "辽宁", "南海诸岛"]; //中国省份/地区
    this.country = ['中国', '美国', '英国', '韩国', '荷兰', '日本']; //海外节点国家/地区(暂时只有这些)
    this.nameMap = { 'China': '中国', 'United States of America': '美国', 'United Kingdom': '英国', 'South Korea': '韩国', 'Netherlands': '荷兰', 'Japan': '日本' }; //海外地图对应国家的中文名称
    this.servercountryArr = [{ key: "中国", val: "China" }, { key: "美国", val: "UnitedStatesofAmerica" }, { key: "英国", val: "UnitedKingdom" }, { key: "韩国", val: "SouthKorea" }, { key: "荷兰", val: "Netherlands" }, { key: "日本", val: "Japan" }]; //现有的节点(先写死)
    this.toolWorldFormat = []; //海外地区tooltip format
    this.option = {
        title: {
            text: title,
            x: 'center'
        },
        tooltip: {
            trigger: 'item'
        },
        legend: {
            orient: 'vertical',
            x: 'left',
            data: [],
            show: false
        },
        dataRange: {
            show: false,
            orient: 'vertical',
            min: 0,
            itemGap: 5,
            max: 8000,
            x: 'left',
            y: 'bottom',
            text: null,
            calculable: false,
            color: ['#24aa1d', '#42dd3f', '#bef663', '#f6ed44', '#f69833', '#e61610'],
            splitList: null
        },
        toolbox: {
            show: false,
            orient: 'vertical',
            x: 'right',
            y: 'center',
            feature: {
                mark: { show: true },
                dataView: { show: true, readOnly: false },
                restore: { show: true },
                saveAsImage: { show: true }
            }
        },
        series: []
    };
};
var echarts_fn = Echarts.prototype;
//地图入口
echarts_fn.init = function () {
    var _this = this;
    var dc = document.getElementById(_this.chartid);
    if (!dc) return;
    var chart = echarts.init(dc);
    chart.setOption(_this.option);
    _this.getNullProvinces();
    chart.on(echarts.config.EVENT.CLICK, function (param) {
        switch (_this.type) {
            case "ping":
                _this.getOneInfo_ping(param);
                break;
            case "speedtest":
            case "speedworld":
                _this.getOneInfo_speedTest(param, chart);
                break;
            case "speedcom":
                //_this.getOneInfo_speedCom(param);
                break;
        }
    });
    return chart;
};

function bindChart(sd, myCharts, myCharts1) {
    if (!sys.ie || sys.ie > 8) {
        if (this.type == "speedworld")
            this.setBindChart_world(sd, myCharts);
        else
            this.setBindChart(sd, myCharts);
    } else {
        var _this = this, complage_count = 0, list = [], _complage_count = 0, _list = [], hideElm = [];
        if (_this.type == "ping") {
            list = $("#speedlist div.row");
            complage_count = $("#speedlist div.row[state=2]").length;
            hideElm = $(".PingCentWrap");
        } else if (_this.type == "speedcom") {
            list = $("#speedlist div.row dl[flag=test]");
            complage_count = $("#speedlist div.row dl[flag=test][state=2]").length;
            _list = $("#speedlist div.row dl[flag=test1]");
            _complage_count = $("#speedlist div.row dl[flag=test1][state=2]").length;
            hideElm = $(".MapChartOne");
        } else {
            list = $("#speedlist div.row dl");
            complage_count = $("#speedlist div.row dl[state=2]").length;
            hideElm = $(".MapChartOne");
        }
        if (list.length == complage_count || (_list.length != 0 && _list.length == _complage_count)) {
            hideElm.removeClass("autohide");
            //修复IE下BUG（如果当前的charts元素为隐藏）  要重新实例化地图 否则点击事件无效 +_+ IE下还会有错误   ~~o(>_<)o ~~
            try {
                //var ECHARTS = new Echarts(this.chartid);
                //ECHARTS.type = this.type;
                //myCharts = ECHARTS.init();
                if (this.type == "speedworld") this.setBindChart_world(sd, myCharts);
                else this.setBindChart(sd, myCharts);
            } catch (e) {

            }
        }
    }
    myCharts.setOption(this.option, true);
    window.onresize = function () {
        myCharts.resize();
        if (myCharts1)
            myCharts1.resize();
    };
    $("#" + this.chartid).prev().hide();
    $("#" + this.chartid).show();
    $(".MapChartResult").removeClass("autohide");
    $(".IcpMain02").removeClass("autohide");
    $(".MapChartOne-right").removeClass("autohide");
}
//配置地图
//传两个myCharts 当窗口大小发生改变时用到
echarts_fn.bindChart = function (sd, myCharts, myCharts1) {
    if (!sys.ie || sys.ie > 8) {
        if (this.type == "speedworld")
            this.setBindChart_world(sd, myCharts);
        else
            this.setBindChart(sd, myCharts);
    } else {
        var _this = this, complage_count = 0, list = [], _complage_count = 0, _list = [], hideElm = [];
        if (_this.type == "ping") {
            list = $("#speedlist div.row");
            complage_count = $("#speedlist div.row[state=2]").length;
            hideElm = $(".PingCentWrap");
        } else if (_this.type == "speedcom") {
            list = $("#speedlist div.row dl[flag=test]");
            complage_count = $("#speedlist div.row dl[flag=test][state=2]").length;
            _list = $("#speedlist div.row dl[flag=test1]");
            _complage_count = $("#speedlist div.row dl[flag=test1][state=2]").length;
            hideElm = $(".MapChartOne");
        } else {
            list = $("#speedlist div.row");
            complage_count = $("#speedlist div.row[state=2]").length;
            hideElm = $(".MapChartOne");
        }
        if (list.length == complage_count || (_list.length != 0 && _list.length == _complage_count)) {
            hideElm.removeClass("autohide");
            //修复IE下BUG（如果当前的charts元素为隐藏）  要重新实例化地图 否则点击事件无效 +_+ IE下还会有错误   ~~o(>_<)o ~~
            try {
                //var ECHARTS = new Echarts(this.chartid);
                //ECHARTS.type = this.type;
                //myCharts = ECHARTS.init();
                if (this.type == "speedworld") this.setBindChart_world(sd, myCharts);
                else this.setBindChart(sd, myCharts);
            } catch (e) {

            }
        }
    }
    myCharts.setOption(this.option, true);
    window.onresize = function () {
        myCharts.resize();
        if (myCharts1)
            myCharts1.resize();
    };
    $("#" + this.chartid).prev().hide();
    $("#" + this.chartid).show();
    $(".MapChartResult").removeClass("autohide");
    $(".IcpMain02").removeClass("autohide");
    $(".MapChartOne-right").removeClass("autohide");
};

//绑定地图数据
echarts_fn.setBindChart = function (sd, myCharts) {
    var _this = this;
    this.option.dataRange.show = true;
    this.setSplitList(this.option);
    this.option.series = [];
    this.getOptionData("电信", sd.seriesData, 'china', "#feb41c", { x: '0', width: '85%' });
    this.getOptionData("多线", sd.seriesData1, 'china', "#feb41c", { x: '0', width: '85%' });
    this.getOptionData("联通", sd.seriesData2, 'china', "#feb41c", { x: '0', width: '85%' });
    this.getOptionData("移动", sd.seriesData3, 'china', "#feb41c", { x: '0', width: '85%' });
    this.getOptionData("暂无检测点", this.nullserver, 'china', "#fff", { x: '0', width: '85%' });
    this.option.tooltip.formatter = function (params, ticket, callback) {
        return _this.getTooltip(sd.seriesData, sd.seriesData1, sd.seriesData2, sd.seriesData3, params);
    }
};
//绑定地图数据(海外)
echarts_fn.setBindChart_world = function (sd, myCharts) {
    var _this = this;
    this.setSplitList(this.option);
    this.option.dataRange.show = true;
    for (var i = 0; i < this.servercountryArr.length; i++) {
        var ckey = this.servercountryArr[i].key;
        var ct = this.getCountry(ckey);
        this.getOptionData(ckey, sd.seriesData, ct.mapType, "#feb41c", ct.mapLocation);
    }
    this.option.tooltip.formatter = function (params, ticket, callback) {
        return _this.getTooltip_world(_this.toolWorldFormat, params);
    }
};
//获取各国家在charts容器中的定位
echarts_fn.getCountry = function (key) {
    var rh = $(".MapChartOne-right").height();
    if (!this.isertry)
        $(".mapleft").height(rh > 500 ? 560 : 300);
    if (key.indexOf("中国") >= 0)
        return { mapType: "world|China", mapLocation: { x: '15%', y: '14%', width: '30%', height: '40%' } };
    else if (key.indexOf("美国") >= 0 || key.indexOf("洛杉矶") >= 0)
        return { mapType: "world|United States of America", mapLocation: { x: '52%', y: '14%', width: '30%', height: '40%' } };
    else if (key.indexOf("英国") >= 0) {
        if (rh > 500)
            return { mapType: "world|United Kingdom", mapLocation: { x: '25%', y: '43%', width: '15%', height: '25%' } };
        else
            return { mapType: "world|United Kingdom", mapLocation: { x: '20%', y: '62%', width: '25%', height: '30%' } };
    }
    else if (key.indexOf("韩国") >= 0) {
        if (rh > 500)
            return { mapType: "world|South Korea", mapLocation: { x: '63%', y: '43%', width: '10%', height: '15%' } };
        else
            return { mapType: "world|South Korea", mapLocation: { x: '38%', y: '62%', width: '25%', height: '30%' } };
    }
    else if (key.indexOf("荷兰") >= 0) {
        if (rh > 500)
            return { mapType: "world|Netherlands", mapLocation: { x: '25%', y: '70%', width: '15%', height: '25%' } };
        else
            return { mapType: "world|Netherlands", mapLocation: { x: '52%', y: '62%', width: '25%', height: '30%' } };
    }
    else if (key.indexOf("日本") >= 0) {
        if (rh > 500)
            return { mapType: "world|Japan", mapLocation: { x: '60%', y: '70%', width: '15%', height: '25%' } };
        else
            return { mapType: "world|Japan", mapLocation: { x: '70%', y: '62%', width: '25%', height: '30%' } };
    }
};
//获取没有监测点的省份/地区
echarts_fn.getNullProvinces = function () {
    if (serverlist) {
        for (var i = 0; i < this.provinces.length; i++) {
            var province = this.provinces[i];
            if (serverlist.toString().indexOf(province) < 0) {
                this.nuprovince.push(province);
                if (this.option && this.nullserver)
                    this.nullserver.push({ name: province, selected: true });
            }
        }
    }
};
//Ping的结果级别
echarts_fn.setSplitList = function () {
    if (this.type == "ping") {
        this.option.dataRange.splitList = [
            { start: 0.1, end: 50, label: '<=50' },
            { start: 50.1, end: 100, label: '50~100' },
            { start: 100.1, end: 150, label: '100~150' },
            { start: 150.1, end: 200, label: '150~200' },
            { start: 200.1, label: '>200' },
            { end: 0, label: '超时' }
        ];
    } else {
        this.option.dataRange.splitList = [
            { start: 0.1, end: 400, label: '<=400' },
            { start: 400.1, end: 800, label: '400~800' },
            { start: 800.1, end: 1200, label: '800~1200' },
            { start: 1200.1, end: 1600, label: '1200~1600' },
            { start: 1600.1, label: '>1600' },
            { end: 0, label: '超时' }
        ]
    }
};
//配置一个seriesData
echarts_fn.getOptionData = function (name, data, maptype, empcolor, mapLocation) {
    this.option.series.push({
        name: name,
        type: 'map',
        mapType: maptype,
        mapValueCalculation: 'average',
        mapValuePrecision: 1,
        roam: false,
        mapLocation: mapLocation,
        itemStyle: {
            normal: { borderWidth: 1.5, borderColor: "#ddd", label: { show: (maptype == "world" ? (name == "暂无检测点" ? false : true) : true) } },
            emphasis: { borderWidth: 1.5, borderColor: "#ddd", color: empcolor, label: { show: (maptype == "world" ? (name == "暂无检测点" ? false : true) : true) } }
        },
        data: data,
        nameMap: this.nameMap//解析世界地图对应的中文名称
    });
};
//配置提示框
echarts_fn.getTooltip = function (seriesData, seriesData1, seriesData2, seriesData3, prams) {
    var res = this.getTooltipOne(seriesData, prams, "电信"), res1 = this.getTooltipOne(seriesData1, prams, "多线"), res2 = this.getTooltipOne(seriesData2, prams, "联通"), res3 = this.getTooltipOne(seriesData3, prams, "移动"), resnull = "";
    for (var i = 0; i < this.nuprovince.length; i++) {
        if (prams[1] == this.nuprovince[i]) {
            resnull = "暂无监测点";
        }
    }
    var avg = "";
    return prams[1] + avg + '<br/>' + (res ? res + '<br/>' : '') + (res1 ? res1 + '<br/>' : '') + (res2 ? res2 + '<br/>' : '') + (res3 ? res3 + '<br/>' : '') + resnull;
};
//配置提示框(海外)
echarts_fn.getTooltip_world = function (seriesData, prams) {
    var res = this.getTooltipOne(seriesData, prams, "world"), resnull = "";
    for (var i = 0; i < this.nuprovince.length; i++) {
        if (prams[1] == this.nuprovince[i]) {
            resnull = "暂无监测点";
        }
    }
    return prams[1] + '<br/>' + (res ? res + '<br/>' : '') + resnull;
};
//配置一个提示框
echarts_fn.getTooltipOne = function (seriesData, prams, type) {
    var res = "";
    for (var i = 0; i < seriesData.length; i++) {
        var sd = seriesData[i];
        if (type == "world") {
            if (sd.sc.indexOf(prams[1]) >= 0 || (prams[1] == '中国' && (sd.sc.indexOf('香港') >= 0 || sd.sc.indexOf('澳门') >= 0))) {
                var _type = sd.sc.split('-')[0];
                var _value = sd.sc.split('-')[1];
                res = (res ? res + '<br/>' + _type + '：' : _type + '：') + (_value == "0" ? "超时" : _value) + (_value == "0" ? "" : "ms");
            }
        } else {
            if (prams[1] == sd.name) {
                res = (res ? res + '<br/>' + type + '：' : type + '：') + (sd.value == "0" ? "超时" : (sd.value == "0.5" ? "<1" : sd.value)) + (sd.value == "0" ? "" : "ms");
            }
        }
    }
    //正在检测中
    if (serverlist.length && !res) {
        //遍历当前所有检测路线
        for (var i = 0; i < serverlist.length; i++) {
            if (serverlist[i].indexOf(prams[1]) >= 0 && serverlist[i].indexOf(type) >= 0) {
                return res ? res : type + "：正在检测...";
            }
        }
    }
    return res;
};
//点击省份（地区）  获取节点数据
//ping
echarts_fn.getOneInfo_ping = function (param) {
    var province = param.name;
    for (var i = 0; i < this.nullserver.length; i++) {
        if (this.nullserver[i].name.indexOf(province) >= 0) return; //无检测点，则不执行单击事件
    }
    $("#pingImg").hide();
    var serverlist = $("#speedlist span[name='city']");
    var listr = '';
    var cnt = 0;
    var showobj = $("#idc-item");
    var ttxt = $("a.curt").text();
    serverlist.each(function () {
        var server = $(this).html().replace(/\[\S+\]/, "");
        var line = /\[\S+\]/.exec($(this).html()).toString().replace('[', '').replace(']', '');
        var flag = false;
        switch (ttxt) {
            case "全部":
                flag = server.indexOf(province) >= 0;
                showobj = $("#jcone");
                if (listr == "")
                    listr = '<li class="col-blue02 PCRLhead"><span class="w60 tc">线路</span><span class="w210">监测点</span><span class="w90 tr">赞助商</span></li>';
                break;
            case "电信":
                flag = server.indexOf(province) >= 0 && $("a.curt").text() == line;
                $("#dianx .areaC").show();
                $("#dianx .areaC>p span").text(province);
                showobj = $("#dianx .areaC ul.PCRightList");
                if (listr == "")
                    listr = '<li class="col-blue02 PCRLhead"><span class="w251">监测点</span><span class="w160 tr">赞助商</span></li>';
                break;
            case "多线":
                flag = server.indexOf(province) >= 0 && $("a.curt").text() == line;
                $("#duox .areaC").show();
                $("#duox .areaC>p span").text(province);
                showobj = $("#duox .areaC ul.PCRightList");
                if (listr == "")
                    listr = '<li class="col-blue02 PCRLhead"><span class="w251">监测点</span><span class="w160 tr">赞助商</span></li>';
                break;
            case "联通":
                flag = server.indexOf(province) >= 0 && $("a.curt").text() == line;
                $("#liant .areaC").show();
                $("#liant .areaC>p span").text(province);
                showobj = $("#liant .areaC ul.PCRightList");
                if (listr == "")
                    listr = '<li class="col-blue02 PCRLhead"><span class="w251">监测点</span><span class="w160 tr">赞助商</span></li>';
                break;
            case "移动":
                flag = server.indexOf(province) >= 0 && $("a.curt").text() == line;
                $("#yid .areaC").show();
                $("#yid .areaC>p span").text(province);
                showobj = $("#yid .areaC ul.PCRightList");
                if (listr == "")
                    listr = '<li class="col-blue02 PCRLhead"><span class="w251">监测点</span><span class="w160 tr">赞助商</span></li>';
                break;
            default:
                flag = server.indexOf(province) >= 0;
                showobj = $("#jcone");
                if (listr == "")
                    listr = '<li class="col-blue02 adrssL PCRLhead"><span class="w60 tc">线路</span><span class="w210">监测点</span><span class="w90 tr">赞助商</span></li>';
                break;
        }
        if (flag) {
            var ip = $(this).parents("li").find("[name='ip']").text().replace(/\s+/g, "");
            var ipstr = ip.indexOf('.') < 0 ? '' : '<a href="javascript:" class="sip">[' + ip + ']</a>';
            if (ip == "超时")
                ip = "超时";
            else if (ip == "-" || !ip)
                ip = "检测中...";
            else
                ip = $(this).parents("li").find("[name='responsetime']").html();
            if (ttxt == "全部") {
                listr += '<li class="PCRLcent"><strong class="w60">' + line + '</strong><p class="w210"><span class="fl">' + $(this).html().replace(/\[\S+\]/, '') + '&nbsp;' + ipstr + '</span><span class="fr"' + (ip == "超时" ? "style='color:red'" : "") + '>' + ip + '</span></p><p class="w90 tr">' + $(this).parents("li").find("span:last").html() + '</p></li>';
                cnt++;
            }
            else {
                var cs = $(this).html();
                if (cs.indexOf("内蒙古") >= 0 || cs.indexOf("黑龙江") >= 0) {
                    cs = cs.substring(3);
                    cs = cs.replace(/\[\S+\]/, '');
                } else if (cs.indexOf("台湾") >= 0 || cs.indexOf("香港") >= 0 || cs.indexOf("重庆") >= 0 || cs.indexOf("上海") >= 0 || cs.indexOf("北京") >= 0 || cs.indexOf("天津") >= 0) {
                    cs = cs.replace(/\[\S+\]/, '');
                } else {
                    cs = cs.substring(2);
                    cs = cs.replace(/\[\S+\]/, '');
                }
                listr += '<li class="PCRLcent"><p class="w251"><span class="fl">' + cs + '&nbsp;' + ipstr + '</span><span class="fr"' + (ip == "超时" ? "style='color:red'" : "") + '>' + ip + '</span></p><p class="w160 tr">' + $(this).parents("li").find("span:last").html() + '</p></li>';
                cnt++;
            }
        }
    });
    showobj.html(cnt == 0 ? '<li class="PCRLcent bor-r1s"><p class="tc ww100">暂无检测点</p></li>' : listr).show();
};
function getOneInfo_ping(path) {
    var province = path.areas.trim();
    //for (var i = 0; i < this.nullserver.length; i++) {
    //    if (this.nullserver[i].name.indexOf(province) >= 0) return; //无检测点，则不执行单击事件
    //}
    $("#pingImg").hide();
    idc(province);
    var serverlist = $("#speedlist span[name='city']");
    var listr = '';
    var cnt = 0;
    var showobj = $(".idc-item");

    showobj.show();
    showobj.find("span[name='area']").html(province);
    showobj.find("a").attr("ctype", province);
    showobj.show();
   
    //var area = $("#result-list span[name='areaTag']").html();
    //if (area) {
    //    showAreaItem(area);
    //}
    showAreaItem(province);
};

function showAreaItem(area) {
    var list = $("#speedlist .listw");
    if (area) {
        if (list.length) {
            list.hide();
            list.find("[name='city']:contains('" + area + "')").parents(".listw").show();
            $("span[name='areaTag']").html(area);
            $("a[ctype]").show();
        }
    } else {
        list.show();
        $("a[ctype='']").hide();
        $("span[name='areaTag']").html('');
    }
    if($("div.listw[state!=2],dl.row[state!=2]").length == 0)
    $("a.retry:visible").length > 0 ? $(".allRetry").show() : $(".allRetry").hide();
}

//speedtest
echarts_fn.getOneInfo_speedTest = function (param, chart) {
    var province = param.name;
    for (var i = 0; i < this.nullserver.length; i++) {
        if (this.nullserver[i].name.indexOf(province) >= 0) return; //无检测点，则不执行单击事件
    }
    $("#speedImg").hide();
    var serverlist = $("#speedlist [name='city']");
    var listr = '<p class="clearfix pt15 pb10 h30"><span class="fz16 col-blue02 fl h30 lh30">' + province + '地区监测点</span></p>';
    if (this.type == "speedtest") {
    }
        //listr += '<li class="col-blue02 PCRLhead"><span class="w89 tc">线路</span><span class="w250">监测点</span><span class="w130 tr">赞助商</span></li>';
    else
        listr += '<li class="col-blue02 PCRLhead"><span class="w250">监测点</span><span class="w250 tr">赞助商</span></li>';
    var _this = this;
    serverlist.each(function () {
        var server = $(this).html().replace(/\[\S+\]/, "");
        if (server.indexOf(province) >= 0) {
            var ip = $(this).parents("div").siblings().find("[name='ip']").text().replace(/\s+/g, "");
            if (ip == "超时")
                ip = "超时";
            else if (ip == "-" || !ip)
                ip = "检测中...";
            else
                ip = $(this).parents("div").siblings().find("[name='alltime']").html();
            if (_this.type == "speedtest") {
                listr += '<tr class="list-cont"><td class="sp"><span>' + $(this).html().replace(/\[.*?]/, '') + '</span><a href="javascript:">' + ip + '</a><span class="col-green fr">' + $(this).parents(".listw").find("[name='support']").html() + '</span></td>';
                listr += '<td class="sp tr"><a href="http://www.7yc.com/rent.html" target="_blank" rel="nofollow">国内高防服务器 高防IDC</a></td></tr>';
            }
                //listr += '<li class="PCRLcent"><strong class="w89">' + /\[\S+\]/.exec($(this).html()).toString().replace('[', '').replace(']', '') + '</strong><p class="w250"><span class="fl">' + $(this).html().replace(/\[.*?]/, '') + '</span><span class="fr"' + (ip == "超时" ? "style='color:red'" : "") + '>' + ip + '</span></p><p class="w130 tr">' + $(this).parents("li").find("[name='support']").html() + '</p></li>';
            else
                listr += '<li class="PCRLcent"><p class="w250"><span class="fl">' + $(this).html().replace(/\[.*?]/, '') + '</span><span class="fr">' + ip + '</span></p><p class="w250 tr">' + $(this).parents("li").find("[name='support']").html() + '</p></li>';
        }
    });
    $("#jcone").html(listr).removeClass("autohide");

    if (this.type == "speedworld") {
        if (!sys.ie || sys.ie > 8)//IE8及以下兼容性问题   则不执行
            this.countryClick(chart);
    }
};
function getOneInfo_speedTest(path) {
    var province = path.areas.trim();
    //for (var i = 0; i < this.nullserver.length; i++) {
    //    if (this.nullserver[i].name.indexOf(province) >= 0) return; //无检测点，则不执行单击事件
    //}
    $("#speedImg").hide();
    idc(province);

    var showobj = $(".idc-item");

    showobj.show();
    showobj.find("span[name='area']").html(province);
    showobj.find("a").attr("ctype", province);

    //var area = $("#result-list span[name='areaTag']").html();
    //if (area) {
    //    showAreaItem(area);
    //}
    showAreaItem(province);
};

function idc(province) {
    var html = '<tr class="list-head"><td class="sp">机房名称</td><td class="sp tr">运营商</td></tr>';
    jQuery.ajax({
        type: "get",
        url: "http://api.whois.chinaz.com/idc/get?pro=" + province,
        dataType: 'jsonp',
        beforeSend: function () {
            var loadHtml =html+ '<tr class="list-cont"><td class="sp tc" colspan="2"><img src="' + imgurlbase + '/public/spinner.gif"/>正在加载...</td></tr>';
            $("#idc-list").html(loadHtml);
        },
        success: function (data) {
            for (var i = 0; i < data.length; i++) {
                html += '<tr class="list-cont"><td class="sp"><span title="' + data[i].jifang + '">' + data[i].jifang + '</span></td><td class="sp tr"><a href="' + data[i].link + '" target="_blank" title="' + data[i].name + '" rel="nofollow">' + data[i].name + '</a></td></tr>';
            }
            if (data.length == 0) {
                html += '<tr class="list-cont"><td class="sp" colspan="2">暂无机房信息</td></tr>';
            }
            $("#idc-list").html(html);
        }
    });
}

//speedcom
echarts_fn.getOneInfo_speedCom = function (param) {
    var province = param.name;
    for (var i = 0; i < this.nullserver.length; i++) {
        if (this.nullserver[i].name.indexOf(province) >= 0) return; //无检测点，则不执行单击事件
    }
    var serverlist = $("#speedlist [name='city']");
    var listr = '';
    serverlist.each(function () {
        var server = $(this).html().replace(/\[\S+\]/, "");
        if (server.indexOf(province) >= 0) {
            var ip = $(this).parents("li").find("[name='ip']:eq(0)").text().replace(/\s+/g, "");
            var ip1 = $(this).parents("li").find("[name='ip']:eq(1)").text().replace(/\s+/g, "");
            if (ip == "超时")
                ip = "超时";
            else if (ip == "-" || !ip)
                ip = "检测中...";
            else
                ip = $(this).parents("li").find("[name='alltime']:eq(0)").html();

            if (ip1 == "超时")
                ip1 = "超时";
            else if (ip1 == "-" || !ip1)
                ip1 = "检测中...";
            else
                ip1 = $(this).parents("li").find("[name='alltime']:eq(1)").html();

            var alltime = $(this).parents("li").find("[name='alltime']:eq(0)").html();
            var alltime1 = $(this).parents("li").find("[name='alltime']:eq(1)").html();
            var dnstime = $(this).parents("li").find("[name='dnstime']:eq(0)").html();
            var dnstime1 = $(this).parents("li").find("[name='dnstime']:eq(1)").html();
            var conntime = $(this).parents("li").find("[name='conntime']:eq(0)").html();
            var conntime1 = $(this).parents("li").find("[name='conntime']:eq(1)").html();
            var downtime = $(this).parents("li").find("[name='downtime']:eq(0)").html();
            var downtime1 = $(this).parents("li").find("[name='downtime']:eq(1)").html();
            var support = $(this).parents("li").find("[name='support']").html();
            listr += '<li class="PCRLcent isrem"><strong class="w70 tc">{0}</strong><p class="w100 tc suplast">{1}</p><p class="w70 tc"{2}>{3}</p><p class="w70 tc">{4}</p><p class="w70 tc">{5}</p><p class="w70 tc suplast">{6}</p><p class="w70 tc">{7}</p><p class="w70 tc">{8}</p><p class="w70 tc">{9}</p><p class="w70 tc suplast">{10}</p><p class="w130 tr">{11}</p></li>'.format(/\[\S+\]/.exec($(this).html()).toString().replace('[', '').replace(']', ''), $(this).html().replace(/\[.*?]/, ''), (ip == "超时" ? "style='color:red'" : ""), ip, dnstime, conntime, downtime, ip1, dnstime1, conntime1, downtime1, support);
        }
    });
    $("#jcone .isrem").remove();
    $("#jcone").append(listr).removeClass("autohide");

};
//海外节点点击时  节点数超出  则重新排列相关国家/地区的位置
echarts_fn.countryClick = function (chart) {
    var rh = $(".MapChartOne-right").height();
    $(".mapleft").height(rh > 500 ? 560 : 300);
    var series = this.option.series;
    for (var i = 0; i < series.length; i++) {
        var item = series[i];
        switch (item.name) {
            case "英国":
                if (rh > 500) {
                    item.mapLocation = { x: '25%', y: '43%', width: '15%', height: '25%' };
                }
                else {
                    item.mapLocation = { x: '20%', y: '62%', width: '25%', height: '30%' }
                }
                break;
            case "韩国":
                if (rh > 500) {
                    item.mapLocation = { x: '63%', y: '43%', width: '10%', height: '15%' };
                }
                else {
                    item.mapLocation = { x: '38%', y: '62%', width: '25%', height: '30%' };
                }
                break;
            case "荷兰":
                if (rh > 500) {
                    item.mapLocation = { x: '25%', y: '70%', width: '15%', height: '25%' };
                }
                else {
                    item.mapLocation = { x: '52%', y: '62%', width: '25%', height: '30%' };
                }
                break;
            case "日本":
                if (rh > 500) {
                    item.mapLocation = { x: '60%', y: '70%', width: '15%', height: '25%' };
                }
                else {
                    item.mapLocation = { x: '70%', y: '62%', width: '25%', height: '30%' };
                }
                break;
        }
        this.option.series[i].mapLocation = item.mapLocation;
    }
    chart.setOption(this.option, true);
    chart.resize();
};
//var ECHARTS = new Echarts("charts","title");
//var chart = ECHARTS.init();
//ECHARTS.option.series = [];
//chart.setOption(ECHARTS.option);




