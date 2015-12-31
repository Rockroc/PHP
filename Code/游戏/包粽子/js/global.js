/**
 * Created by joneszhuchinagd on 2015/6/13.
 * 全局变量  全局函数
 */
var Global;
(function (Global) {
    /*
     * 全局数据
     * */
    Global.locationHref = window.location.href.replace(/\w+\.(php|html|htm).*$/, "");
    Global.innerW = window.innerWidth;
    Global.innerH = window.innerHeight;
    /*
     * 微信分享
     * */
    //demo
    //Global.weixinShare({
    //    "timelineTitle":"我在U+智慧生活APP考试中，变成了",
    //    "link":Global.locationHref+"index.php",
    //    "appmessageTitle":document.getElementsByTagName("title")[0].innerText,
    //    "appmessageDesc":"我在U+智慧生活APP考试中，变成了"
    //});

    Global.weixinShare = function (data) {
        wx.ready(function () {
            // config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后，config是一个客户端的异步操作，所以如果需要在页面加载时就调用相关接口，则须把相关接口放在ready函数中调用来确保正确执行。对于用户触发时才调用的接口，则可以直接调用，不需要放在ready函数中。
            wx.onMenuShareTimeline({
                title: data.timelineTitle, // 分享标题
                link: data.link, // 分享链接
                imgUrl: Global.locationHref + 'images/icon.jpg',// 分享图标
                success: function () {
                },
                cancel: function () {
                    alert("您还没分享成功哟！");
                }
            });
            wx.onMenuShareAppMessage({
                title: data.appmessageTitle, // 分享标题
                desc: data.appmessageDesc, // 分享描述
                link: data.link, // 分享链接
                imgUrl: Global.locationHref + 'images/icon.jpg',// 分享图标
                success: function () {
                },
                cancel: function () {
                    alert("您还没分享成功哟！");
                }
            });
        });
    };
    /*
     * 图片预加载
     * */
    //demo
    // Global.preload([
    //    "loadingBg.jpg,clock.png"
    //], function (total, arr) {
    //    if (total == arr.length) {
    //        $pageArr.eq(0).fadeIn();
    //        Global.preload([
    //            "back1.jpg",
    //            "start.png"
    //        ], function (total, arr) {
    //            if (total == arr.length) {
    //                $pageArr.eq(0).fadeOut();
    //                $pageArr.eq(1).fadeIn();
    //            }
    //        });
    //    }
    //});

    Global.preload = function (arr, callback) {
        var total = 0;
        for (var i = 0; i < arr.length; i++) {
            var oImg = new Image();
            oImg.src = Global.locationHref + "images/" + arr[i];
            if (oImg.complete) {
                total++;
                callback(total, arr);
                continue;
            }
            oImg.onload = function () {
                this.onload = null;
                total++;
                callback(total, arr);
            }
        }
    }
    /*
     * 阻止浏览器默认事件
     * */
    Global.preventDefault = function () {
        var obody = document.body;
        obody.addEventListener("touchstart", function (e) {
            e.preventDefault();
        });
        obody.addEventListener("touchmove", function (e) {
            e.preventDefault();
        });
        obody.addEventListener("touchend", function (e) {
            e.preventDefault();
        });
    }

    /*
     * 生成随机整数
     * */
    Global.randInt = function (min, max) {
        return parseInt(Math.random() * (max - min + 1) + min);
    }

})(Global || (Global = {}));