jQuery(document).ready((function(e){function t(){var t=parseInt(e("#fc_server_timestamp").data("timestamp")),n=new Date(1e3*t),m=n.getUTCFullYear()+"-"+("0"+(n.getUTCMonth()+1)).slice(-2)+"-"+("0"+n.getUTCDate()).slice(-2)+" "+("0"+(n.getUTCHours()%12||12)).slice(-2)+":"+("0"+n.getUTCMinutes()).slice(-2)+(n.getUTCHours()<12?" am":" pm");e("#fc_server_timestamp").text("Server Time: "+m),e("#fc_server_timestamp").data("timestamp",t+60)}e(".fluentcrm_submenu_items a").on("click",(function(){window.innerWidth>768&&e(this).closest(".fluentcrm_submenu_items").addClass("fluentcrm_force_hide")})),e(".fluentcrm_menu_item a").on("click",(function(t){e(".fluentcrm_menu_item").removeClass("fluentcrm_active"),e(this).closest(".fluentcrm_menu_item").addClass("fluentcrm_active"),"SPAN"!=t.target.nodeName&&e(".fluentcrm_menu").removeClass("fluentcrm_menu_open")})),jQuery(".fluentcrm_has_sub_items").on("mouseenter",(function(){e(this).find(".fluentcrm_submenu_items").removeClass("fluentcrm_force_hide")})),e(".fluentcrm_handheld").on("click",(function(){e(".fluentcrm_menu").toggleClass("fluentcrm_menu_open")})),jQuery("body").on("click",".components-color-palette__custom-color",(function(e){e.preventDefault()})),t(),setInterval(t,6e4)})),jQuery(document).on("fluentcrm_route_change",(function(e,t){jQuery(".fluentcrm_menu_item").removeClass("fluentcrm_active"),jQuery(".fluentcrm_item_"+t).addClass("fluentcrm_active")}));