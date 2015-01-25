;(function (w, d, $, undefined) {
    'use strict';
    // Avoid `console` errors in browsers that lack a console.
    var method;
    var noop = function () {};
    var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeStamp', 'trace', 'warn'
    ];
    var length = methods.length;
    var console = (window.console = window.console || {});

    while (length--) {
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }

    // Detect desktop browsers
    var isOpera = !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
    // Opera 8.0+ (UA detection to detect Blink/v8-powered Opera)
    var isFirefox = typeof InstallTrigger !== 'undefined';   // Firefox 1.0+
    var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
    // At least Safari 3+: "[object HTMLElementConstructor]"
    var isChrome = !!window.chrome && !isOpera;              // Chrome 1+
    var isIE = /*@cc_on!@*/false || !!document.documentMode; // At least IE6
})(this, document, jQuery);

// Place any jQuery/helper plugins in here.
$(document).ready(function ($) {
    'use strict';
    /*
     * Custom dialog window for delete button
     */
    $(".dialog_delete").on("click", function (a) {
        $("#delete_" + $(this).attr("id")).fadeIn(650);
        a.preventDefault();
    });

    /*
     * Custom cancel button for delete dialog. Acts as a close button
     */
    $(".cancel").on("click", function (a) {
        $(".dialog_hide").fadeOut(650);
        a.preventDefault();
    });

    /*
     * replace this is a menu caption => this-is-a-menu-caption, trim all white space and other characters
     */
    $("#seo-caption").on("keyup", function () {
        var $menulink = $("#seo-caption").val().toLowerCase().replace(/(^\s+|[^a-zA-Z0-9 ]+|\s+$)/g,"").replace(/\s+/g, "-");
        $("#menulink").val($menulink);
    });

    $(".usersearch").on("keyup", function () {
        var $search = $(".usersearch").val();
        if ($.trim($search).length > 2) {
            $.ajax({
                type: "GET",
                url: "/admin/user/search",
                data: {"usersearch": $search},
                dataType: "json",
                contentType: "application/json; charset=utf-8;",
                cache: !1,
                beforeSend: function () {
                    $("#results").val('Fetching data...');
                },
            }).done(function (result) {
                $("#results").empty();
                $.each(result.usersearch, function (key, value) {
                    var $user = $.parseJSON(value);
                    var $del = "<i class='fa fa-times'></i>";
                    if ($user["_deleted"] == 1) {
                        $del = "<i class='fa fa-check'></i>";
                    }
                    $("#results").html("<ul class='table-row'><li class='table-cell'>"+$user["_id"]+"</li><li class='table-cell'>"+$user["_name"]+" "+$user["_surname"]+"</li><li class='table-cell'>"+$user["_username"]+"</li><li class='table-cell'>"+$user["_email"]+"</li><li class='table-cell'>"+$user["_lastLogin"]+"</li><li class='table-cell'>"+$del+"</li><li class='table-cell'>"+$user["_registered"]+"</li><li class='table-cell'><a href='/admin/user/detail/id/"+$user["_id"]+"' class='btn btn-sm blue' title='"+result.details+"'><i class='fa fa-info'></i></a></li><li class='table-cell'><a href='/admin/user/modify/id/"+$user["_id"]+"' class='btn btn-sm orange' title='"+result.modify+"'><i class='fa fa-pencil'></i></a></li><li class='table-cell'><a id='delete_"+$user["_id"]+"' href='#' title='"+result.deleteuser+"' class='btn btn-sm delete dialog_delete'><i class='fa fa-trash-o'></i></a><div id='delete_delete_"+$user["_id"]+"' class='dialog_hide'><p>"+result.delete_text+" &laquo;"+$user["_username"]+"&raquo;&quest;</p><ul><li><a class='btn delete' href='/admin/user/delete/id/"+$user["_id"]+"'><i class='fa fa-trash-o'></i>&nbsp; "+result.deleteuser+"</a></li><li><a class='btn btn-default cancel'><i class='fa fa-times'></i>&nbsp; "+result.cancel+"</a></li></ul></div></li></ul>");
                });
            }).fail(function (a) {
                console.log("Error:" + a); //TODO must create a dialog popup
            });
            $("#results").show();
            $("#linked").hide();
        }
        else {
            $("#results").hide();
            $("#linked").show();
        }
    });
});