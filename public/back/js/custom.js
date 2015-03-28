;(function (w, d, undefined) {
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
})(this, document);

// Place any jQuery/helper plugins in here.
$(document).ready(function ($) {
    'use strict';
    /*
     * Custom dialog window for delete button
     */
    $(".dialog_delete").on("click", function (e) {
        e.preventDefault();
        $("#delete_" + $(this).attr("id")).fadeIn(650);
    });

    /*
     * Custom cancel button for delete dialog. Acts as a close button
     */
    $(".cancel").on("click", function (e) {
        e.preventDefault();
        $(".dialog_hide").fadeOut(650);
    });

    /*
     * replace: this is a menu caption => this-is-a-menu-caption, trim all white space and other characters
     */
    if ($("#titleLink").val() !== undefined) {
        $("#titleLink").val($("#seo-caption").val().toLowerCase().replace(/(^\s+|[^a-zA-Z0-9 ]+|\s+$)/g,"").replace(/\s+/g, "-"));
    }
    if ($("#menulink").val() !== undefined) {
        $("#menulink").val($("#seo-caption").val().toLowerCase().replace(/(^\s+|[^a-zA-Z0-9 ]+|\s+$)/g,"").replace(/\s+/g, "-"));
    }

    $("#seo-caption").on("keyup select change", function () {
        var $seolink = $("#seo-caption").val().toLowerCase().replace(/(^\s+|[^a-zA-Z0-9 ]+|\s+$)/g,"").replace(/\s+/g, "-");
        if ($("#menulink").val() !== undefined) {
            $("#titleLink").val($seolink);
        }
        else {
            $("#menulink").val($seolink);
        }
    });
    var urlSplit = window.location.href.toString().split(window.location.host)[1].split("/");
    $(".ajax-search").on("keyup", function () {
        var $search = $(".ajax-search").val();
        if ($.trim($search).length > 2) {
            $.ajax({
                type: "GET",
                url: "/admin/" + urlSplit[2] + "/search",
                data: {"ajaxsearch": $search},
                dataType: "json",
                contentType: "application/json; charset=utf-8;",
                cache: !1,
            }).done(function (result) {
                $("#results").empty();
                $.each(result.ajaxsearch, function (key, value) {
                    var $ul = $("<ul class='table-row'>");
                    var $val = $.parseJSON(value);
                    for (var property in $val) {
                        if ($val.hasOwnProperty(property)) {
                            if ($val[property] === null || $val[property] === undefined || $val[property] === '') {
                                $ul.append("<li class='table-cell'>&nbsp;</li>");
                            }
                            else {
                                $ul.append("<li data-userId ='"+$val["_id"]+"' class='table-cell'>"+$val[property]+"</li>");
                            }
                        }
                    }
                    $("#results").append($ul);
                });
            }).fail(function (a) {
                console.log("Error:", a.responseText); //TODO must create a dialog popup
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