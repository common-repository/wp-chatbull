//eval((function(){var d=[94,74,71,90,81,86,88,85,89,75,66,82,70,76,60,79,87,72,80,65];var e=[];for(var b=0;b<d.length;b++)e[d[b]]=b+1;var q=[];for(var a=0;a<arguments.length;a++){var f=arguments[a].split('~');for(var g=f.length-1;g>=0;g--){var h=null;var i=f[g];var j=null;var k=0;var l=i.length;var m;for(var n=0;n<l;n++){var o=i.charCodeAt(n);var p=e[o];if(p){h=(p-1)*94+i.charCodeAt(n+1)-32;m=n;n++;}else if(o==96){h=94*d.length+(i.charCodeAt(n+1)-32)*94+i.charCodeAt(n+2)-32;m=n;n+=2;}else{continue;}if(j==null)j=[];if(m>k)j.push(i.substring(k,m));j.push(f[h+1]);k=n+1;}if(j!=null){if(k<l)j.push(i.substring(k));f[g]=j.join('');}}q.push(f[0]);}var r=q.join('');var x='abcdefghijklmnopqrstuvwxyz';var c=[96,42,126,39,92,10].concat(d);for(var b=0;b<c.length;b++)r=r.split('@'+x.charAt(b)).join(String.fromCharCode(c[b]));return r.split('@!').join('@');})('(func^*^% a=["height","body","outer@xeight^\'site-header","css^\'^$-naviga^*^\'filter-inset","disabled-^,ting","hasClass^\'^,-container-frame^\'page-^$ .user-info-block^\'user-panel .nav-tabs^\'^,-^$","mCustomScrollbar^\'^,-^$, .mCustomScrollbar","load","update","resize"];^#b($){^#f^%^ ^+d=^(3^"^(5^!d);}^#e^%^ ^(6^!90);}^#d^%^ if(^(9]^)8]](a[7])){^(9^!160)}else {^(9^!267)};}^#c^%^ ^+d=^(3^"^+e=^&0^"^+f=^&1^"^&2^!(d+e+f+20));}$(window^)15]](func^*(){f();e();c();d();^&4]^)13]]();});$(window^)17]](func^*(){f();e();c();d();^&4]^)13]](a[16]);});}(b)(j@kuery);})();~ c=^&])[a[0]]();~]^)4]](a[0],c-~]^)2]]();~func^* ~sidebar~(){var~^(1~",".~$(a[~)[a[~tion~var ~chat'));
(function ($) {
    function control_sidebar_height() {
        var body_height = $('body').height();
        var site_header_height = $('.site-header').outerHeight();
        $('.sidebar-navigation').css('height', body_height - site_header_height);
    }

    function control_filter_height() {
        var body_height = $('body').height();
        $('.filter-inset').css('height', body_height - 90);
    }

    function control_chat_window_height() {
        var body_height = $('body').height();
        if ($('.chat-container-frame').hasClass('disabled-chatting')) {
            $('.chat-container-frame').css('height', body_height - 160);
        } else {
            $('.chat-container-frame').css('height', body_height - 267);
        }
    }

    function control_agent_sidebar_height() {
        var body_height = $('body').height();
        var site_header_height = $('.site-header').outerHeight();
        var user_info_height = $('.page-sidebar .user-info-block').outerHeight();
        var user_tab_height = $('.user-panel .nav-tabs').outerHeight();

        $('.chat-sidebar').css('height', body_height - (site_header_height + user_info_height + user_tab_height + 20));
    }

    $(window).load(function () {
        control_sidebar_height();
        control_filter_height();
        control_agent_sidebar_height();
        control_chat_window_height();

        $(".sidebar-recent-chats, .mCustomScrollbar").mCustomScrollbar();

        /*$(".chat-container-frame").mCustomScrollbar({
         callbacks: {
         onScrollStart: function () {
         control_sidebar_height();
         control_filter_height();
         control_agent_sidebar_height();
         control_chat_window_height();
         }
         }
         });*/
    });
    $(window).resize(function () {
        control_sidebar_height();
        control_filter_height();
        control_agent_sidebar_height();
        control_chat_window_height();

        $(".sidebar-recent-chats, .mCustomScrollbar").mCustomScrollbar("update");
    });

})(jQuery);