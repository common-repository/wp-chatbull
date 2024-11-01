var iframeOptions = {
    //log: true, // Enable console logging
    inPageLinks: true,
    autoResize: true,
    sizeWidth: true,
    resizedCallback: function (messageData) {
        // Callback fn when resize is received
        if (messageData.height < 100 && messageData.height > 50) {
            if (settings && settings.visitor_widget_type == 'chaticon') {
                messageData.width = 82;
                if (settings.visitor_widget_type == 'chaticon') {
                    if (settings.chat_icon_size == 'medium-size') {
                        messageData.width = 62;
                    } else if (settings.chat_icon_size == 'small-size') {
                        messageData.width = 41;
                    }
                }
            }
        }

        if (settings) {
            var frame_classes = 'chat-cmodule-iframe';
            if (!is_mobile) {
                frame_classes = 'chat-cmodule-iframe iframe-pull-' + settings.window_position;
            } else {
                //frame_classes += ' chat-cmodule-mobile';
            }

            if (settings.visitor_widget_type == 'chaticon') {
                frame_classes += ' chat-cmodule-' + settings.chat_icon_size;
            } else if (settings.visitor_widget_type == 'chatbar') {
                messageData.width = 300;
                frame_classes += ' chat-cmodule-widget-bar';
            }

            document.getElementById("chatbull-frame").className = frame_classes;
        }

        document.getElementById("chatbull-frame").style.width = messageData.width + 'px';
    },
    initCallback: function (iframeData) {
        //console.log(iframeData);
    },
    messageCallback: function (messageData) {
        //console.log(messageData);        
        //alert(messageData.message);
        //document.getElementsByTagName('iframe')[0].iFrameResizer.sendMessage('Hello back from parent page');
    },
    closedCallback: function (id) {
        var chatboxurl = cburl + 'index.php?d=visitors&c=chatbox&m=index&token=' + access_token;
        if (typeof cbuser !== 'undefined') {
            chatboxurl += '&' + buildQueryParam(cbuser);
        }

        chatboxurl += '&' + buildQueryParam(cbwindow);
        chatboxurl += '&page_title=' + encodeURIComponent(ptitle);
        chatboxurl += '&page_url=' + encodeURIComponent(purl);

        window.location = chatboxurl;
    }
}

if (is_mobile) {
    //iframeOptions.scrolling = true;
    iFrameResize(iframeOptions);
} else {
    iFrameResize(iframeOptions);
}

