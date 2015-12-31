/**
 * Created by joneszhuchinagd on 2015/6/12.
 */
$(function () {
    Global.preventDefault();
    var y = "四";
    var z = "高级文盲";
    var score = 0;
    var shareComment;
    shareComment = "我包了" + score + "个粽子，获得" + y + "级包粽子证书，相当于" + z + "！";

    Global.weixinShare({
        "timelineTitle": shareComment,
        "link": Global.locationHref + "index.php",
        "appmessageTitle": document.getElementsByTagName("title")[0].innerText,
        "appmessageDesc": shareComment
    });


    $("body,.main,.page,.share-window").css({
        "width": Global.innerW,
        "height": Global.innerH
    });

    var $pageArr = $(".page");


    Global.preload([
        "logo.png",
        "clock.png",
        "cook.png"
    ], function (total, arr) {
        if (total == arr.length) {
            $pageArr.eq(0).fadeIn();
            Global.preload([
                "direction.png",
                "error.png",
                "gameoverlogo.png",
                "hand.png",
                "logo.png",
                "longzi.png",
                "panel.png",
                "panelBg.jpg",
                "restartBtn.png",
                "sharelogo.png",
                "shareBtn.png",
                "sharecomment.png",
                "slide1.png",
                "slide2.png",
                "slide3.png",
                "timeout.png",
                "yiqibao.png",
                "z1.png",
                "z2.png",
                "z3.png",
                "z4.png",
                "z5.png",
                "z6.png",
                "z7.png",
                "z8.png",
                "z9.png",
                "z10.png",
                "z11.png",
                "z12.png",
                "z13.png",
                "z14.png",
                "go.png",
                "icon.jpg",
                "skip.png",
                "tenBtn.png"
            ], function (total, arr) {
                if (total == arr.length) {
                    setTimeout(function () {
                        $pageArr.eq(0).fadeOut();
                        $pageArr.eq(1).fadeIn();
                    }, 1000);
                }
            });
        }
    });

    /*
     * slider
     * */
    var $sliderMain = $(".slider-main");
    var $sliderContainer = $(".slider-container");
    var $sliderArr = $(".slider-container .slider");
    var $paginationContainer = $(".pagination-container");
    var index = 0;
    var total = $sliderArr.size();
    var width = $sliderMain.width();

    for (var i = 0; i < total; i++) {
        $paginationContainer.append('<em></em>');
    }
    var $paginationArr = $(".pagination-container em");
    $paginationArr.eq(0).addClass("on");

    function nextPagination() {
        $paginationArr.removeClass("on");
        $paginationArr.eq(index).addClass("on");
    }

    var $p1 = $(".comment-container .p1");
    var $p2 = $(".comment-container .p2");
    var comment = [
        {p1: "根据粽子下方箭头指示顺序点击九宫格内方块", p2: '<img src="./images/skip.png"/>'},
        {p1: "点击正确箭头顺序可完成包粽子的动作", p2: '<img src="./images/skip.png"/>'},
        {p1: "在规定时间内包粽子越多得分越高", p2: '<img src="./images/go.png"/>'},
    ]

    function nextComment() {
        $p1.html(comment[index].p1);
        $p2.html(comment[index].p2);
    }

    $sliderContainer.on("touchstart", touchstartCallback);
    $sliderContainer.on("touchmove", touchmoveCallback);
    $sliderContainer.on("touchend", touchendCallback);

    $sliderArr.width(width);
    $sliderContainer.width(total * width);

    var originX;
    var startX;
    var curX;
    var dX;
    var collectX = 0;
    var isWork = false;

    function touchstartCallback(e) {
        e.preventDefault();
        if (!isWork) {
            var touch = e.originalEvent.touches[0];
            startX = touch.clientX;
            originX = touch.clientX;
        }
    }

    function touchmoveCallback(e) {
        e.preventDefault();
        if (!isWork) {
            var touch = e.originalEvent.touches[0];
            curX = touch.clientX;
            if (startX == 0) {
                startX = curX;
                originX = curX;
            }
            dX = curX - startX;
            collectX = Math.round(collectX + dX);
            $sliderContainer.css({
                "-webkit-transform": "translate3d(" + collectX + "px,0px,0px)",
                "transform": "translate3d(" + collectX + "px,0px,0px)"
            });
            startX = curX;
        }
    }

    $sliderContainer.on("webkitTransitionEnd", function () {
        isWork = false;
        startX = 0;
        originX = 0;
        $sliderContainer.css({
            "-webkit-transition-duration": "0ms",
            "transition-duration": "0ms"
        });
    });

    function slide() {
        var dis = -width * index;
        collectX = dis;
        $sliderContainer.css({
            "-webkit-transform": "translate3d(" + dis + "px,0px,0px)",
            "transform": "translate3d(" + dis + "px,0px,0px)",
            "-webkit-transition-duration": "300ms",
            "transition-duration": "300ms"
        });
    }

    function touchendCallback(e) {
        e.preventDefault();
        var direction = startX - originX;
        if (direction !== 0 && !isWork && startX !== 0) {
            isWork = true;
            if (direction < 0) {//往左滑动
                if (index !== total - 1) {
                    index++;
                    nextComment();
                    nextPagination();
                    slide();
                    $sliderArr.eq(index - 1).css("opacity", 0);
                    $sliderArr.eq(index).css("opacity", 1);
                } else {//最后一页了
                    slide();
                }
            } else {//向右滑动
                if (index !== 0) {
                    index--;
                    nextComment();
                    nextPagination();
                    slide();
                    $sliderArr.eq(index + 1).css("opacity", 0);
                    $sliderArr.eq(index).css("opacity", 1);
                } else {//第一页
                    slide();
                }
            }
        }
    }

    /*
     * skip 开始点击
     * */
    $(".comment-container").on("touchend", function (e) {
        $gameWindow.css({
            "-webkit-transform": "translate3d(0px," + windowH + "px,0px)",
            "transform": "translate3d(0px," + windowH + "px,0px)",
            "-webkit-transition-duration": "300ms",
            "transition-duration": "300ms"
        });
        $gamePanel.css({
            "-webkit-transform": "translate3d(0px," + -panelH + "px,0px)",
            "transform": "translate3d(0px," + -panelH + "px,0px)",
            "-webkit-transition-duration": "300ms",
            "transition-duration": "300ms"
        });
        prepare();
    });

    /*
     * 游戏页面初始化
     * */
    var $gameWindow = $(".game-window");
    var $gamePanel = $(".game-panel");
    var $overWindow = $(".over-window");
    var $overPanel = $(".over-panel");
    var $panel = $(".game-panel .panel");
    var $btnArr = $(".game-panel .panel .btn");
    var $zong = $(".game-window .zong");
    var $direction = $(".game-window .direction");
    var $prepare = $(".prepare");
    var $score = $(".score em");
    var windowH;
    var panelH;
    var overwindowH;
    var overpanelH;

    function initGame() {
        windowH = Math.round(Global.innerH * 0.64);
        panelH = Global.innerH - windowH;
        $gameWindow.css({"height": windowH, "top": -windowH});
        $gamePanel.css({"height": panelH, "bottom": -panelH});
        $gameWindow.css({"height": windowH});
        $gamePanel.css({"height": panelH});
    };

    function initOver() {
        overwindowH = Math.round(Global.innerH * 0.53);
        overpanelH = Global.innerH - overwindowH;
        $overWindow.css({"height": overwindowH, "top": -overwindowH});
        $overPanel.css({"height": overpanelH, "bottom": -overpanelH});
        $overWindow.css({"height": overwindowH});
        $overPanel.css({"height": overpanelH});
    }

    initGame();
    initOver();

    function prepare() {
        resetData();
        $zong.hide();
        $direction.hide();
        $prepare.show();
        var timeacc1 = 5;
        var $time = $(".prepare .span1");
        $time.html(timeacc1);
        var timeout = setInterval(function () {
            timeacc1 -= 1;
            if (timeacc1 < 0) {
                clearInterval(timeout);
                $prepare.hide();
                startGame();
                return;
            }
            $time.html(timeacc1);
        }, 1000);
    }

    var zongData = [
        {imgName: "z1.png", num: 3},
        {imgName: "z2.png", num: 3},
        {imgName: "z3.png", num: 3},
        {imgName: "z4.png", num: 3},
        {imgName: "z5.png", num: 3},
        {imgName: "z6.png", num: 3},
        {imgName: "z7.png", num: 2},
        {imgName: "z8.png", num: 2},
        {imgName: "z9.png", num: 2},
        {imgName: "z10.png", num: 3},
        {imgName: "z11.png", num: 3},
        {imgName: "z12.png", num: 3},
        {imgName: "z13.png", num: 3},
        {imgName: "z14.png", num: 3}
    ]

    var curDir = [];
    var oriNum = 0;
    var curNum = 0;
    var curPos = 0;

    var status;

    function resetData() {
        curDir = [];
        oriNum = 0;
        curNum = 0;
        curPos = 0;
        score = 0;
        y = "四";
        z = "高级文盲";
        status = "";
        $score.html(0);
    }

    function startGame() {
        $zong.show();
        $direction.show();
        countTime();
        nextZong();
        enable();
    }

    function nextZong() {
        randZong();
    }


    function randZong() {
        $direction.children().remove();
        var rand = Global.randInt(0, zongData.length - 1);
        var zongzi = zongData[rand];
        oriNum = zongzi.num;
        $zong.css({
            "background": "url(./images/" + zongzi.imgName + ") center center no-repeat",
            "background-position": "0px 0px"
        });
        for (var i = 0; i < zongzi.num; i++) {
            curDir[i] = Global.randInt(1, 8);
            $direction.append('<em class="dir dir' + curDir[i] + '"></em>');
        }
    }

    var $error = $(".game-window .error");

    var timeouterror;
    var timesuccess;

    function checkout(index) {
        if (index == curDir[0]) {
            curDir.shift();
            curNum++;
            curPos -= 640;
            $zong.css({
                "background-position": curPos + "px 0px"
            })
            if (oriNum == curNum) {
                curDir = [];
                curNum = 0;
                oriNum = 0;
                curPos = 0;
                disable();
                setScore();
                clearTimeout(timesuccess);
                timesuccess = setTimeout(function () {
                    if (status !== "gameover") {
                        $btnArr.removeClass("on");
                        enable();
                        randZong();
                    }
                }, 300);
            }
        } else {
            curDir = [];
            curNum = 0;
            oriNum = 0;
            curPos = 0;
            $error.show();
            disable();
            clearTimeout(timeouterror);
            timeouterror = setTimeout(function () {
                if (status !== "gameover") {
                    $btnArr.removeClass("on");
                    enable();
                    randZong();
                    $error.hide();
                }
            }, 300);
        }
    }

    function setScore() {
        score++;
        $score.html(score);
    }

    function countTime() {
        var timeacc = 30;
        var $time = $(".sec em");
        $time.html(timeacc);
        var timeout = setInterval(function () {
            timeacc -= 1;
            if (timeacc < 0) {
                clearInterval(timeout);
                gameover();
                return;
            }
            $time.html(timeacc);
        }, 1000);
    }


    function gameover() {
        disable();
        $error.hide();
        status = "gameover";

        $(".over-window .comment .x").html(score);


        if (1 <= score && score <= 5) {
            y = "四";
            z = "高级文盲";
        } else if (6 <= score && score <= 15) {
            y = "六";
            z = "中级知识分子";
        } else if (16 <= score) {
            y = "八";
            z = "高级知识分子";
        }
        shareComment = "我包了" + score + "个粽子，获得" + y + "级包粽子证书，相当于" + z + "！";
        $(".over-window .comment .y").html(y);
        $(".over-window .comment .z").html(z);
        Global.weixinShare({
            "timelineTitle": shareComment,
            "link": Global.locationHref + "index.php",
            "appmessageTitle": document.getElementsByTagName("title")[0].innerText,
            "appmessageDesc": shareComment
        });

        $overWindow.css({
            "-webkit-transform": "translate3d(0px," + overwindowH + "px,0px)",
            "transform": "translate3d(0px," + overwindowH + "px,0px)",
            "-webkit-transition-duration": "300ms",
            "transition-duration": "300ms"
        });
        $overPanel.css({
            "-webkit-transform": "translate3d(0px," + -overpanelH + "px,0px)",
            "transform": "translate3d(0px," + -overpanelH + "px,0px)",
            "-webkit-transition-duration": "300ms",
            "transition-duration": "300ms"
        });


    }

    function disable() {
        $panel.off("touchstart", panelTouchstartCallback);
        $panel.off("touchmove", panelTouchmoveCallback);
        $panel.off("touchend", panelTouchendCallback);
    }

    function enable() {
        $panel.on("touchstart", panelTouchstartCallback);
        $panel.on("touchmove", panelTouchmoveCallback);
        $panel.on("touchend", panelTouchendCallback);
    }

    function panelTouchstartCallback(e) {
        var target = e.target;
        var index = target.getAttribute("rsi");
        if (target !== e.currentTarget) {
            $btnArr.eq(index - 1).addClass("on");
            checkout(index);
        }

    }

    function panelTouchmoveCallback(e) {
        $btnArr.removeClass("on");
    }

    function panelTouchendCallback(e) {
        $btnArr.removeClass("on");
    }

    /*
     * 结束页面
     * */
    var $shareBtn = $(".shareBtn");
    var $restartBtn = $(".restartBtn");

    $restartBtn.on("touchend", function (e) {
        e.stopPropagation();
        $overWindow.css({
            "-webkit-transform": "translate3d(0px," + -overwindowH + "px,0px)",
            "transform": "translate3d(0px," + -overwindowH + "px,0px)",
            "-webkit-transition-duration": "300ms",
            "transition-duration": "300ms"
        });
        $overPanel.css({
            "-webkit-transform": "translate3d(0px," + overpanelH + "px,0px)",
            "transform": "translate3d(0px," + overpanelH + "px,0px)",
            "-webkit-transition-duration": "300ms",
            "transition-duration": "300ms"
        });
        prepare();
    });


    $shareBtn.on("touchend", function (e) {
        e.stopPropagation();
        $shareWindow.fadeIn(200);

    });
    /*
     * 分享页面
     * */
    var $shareWindow = $(".share-window");
    $shareWindow.on("touchend", function (e) {
        e.stopPropagation();
        $shareWindow.fadeOut(200);
    });
    /*
     * 十元领取
     * */
    var $tenBtn = $(".tenBtn");
    $tenBtn.on("touchend", function (e) {
        e.stopPropagation();
        window.location.href = "http://wap.koudaitong.com/v2/showcase/coupon/fetch?alias=mj5fuinq";
    });

});