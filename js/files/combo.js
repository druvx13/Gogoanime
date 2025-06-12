(function($) {
    'use strict';
    $(document).ready(function() {
        let id = -1; // scope to this ready function
        let timer;
        let value; // scope to this ready function

        $("#search-form #keyword").keyup(function(e) {
            clearTimeout(timer);
            let keyword = $(this).val();
            let keyword_replace = $('#keyword_search_replace').val();
            $('.loader').show();
            if (keyword_replace !== keyword) {
                if (keyword.trim().length >= 2 && value !== keyword) {
                    timer = setTimeout(function() {
                        value = keyword;
                        preload(keyword, id); // id here refers to the one in the outer scope
                        $('#keyword_search_replace').val('');
                        $("#key_pres").val('');
                    }, 1000);
                } else {
                    $('.loader').hide();
                }
            } else {
                $('.loader').hide();
            }
        });

        $("#search-form #keyword").on("paste", function(event) {
            $('.loader').show();
            var element = $(event.target); // Using var as it's function-scoped within this callback
            setTimeout(function() {
                let keyword = $(element).val();
                preload(keyword, id); // id here refers to the one in the outer scope
                $('#keyword_search_replace').val('');
                $("#key_pres").val('');
            }, 1000);
        });

        $("#search-form #keyword").focus(function(e) {
            let keyword = $(this).val();
            if (keyword.trim().length >= 2) {
                if ($('#header_search_autocomplete').html() !== '') {
                    $("#header_search_autocomplete").show();
                }
            }
        });

        $("#search-form #keyword").blur(function() {
            // Added a slight delay to allow click on autocomplete items
            setTimeout(function() {
                if (!$("#header_search_autocomplete").is(':hover')) {
                    $("#header_search_autocomplete").hide();
                }
            }, 200);
        });

        $("#search-form #keyword").keydown(function(e) {
            let keyword = $(this).val();
            let key_pres = $("#key_pres").val();
            if (e.keyCode === 13) { // Enter key
                e.preventDefault();
                if (key_pres !== '') {
                    let alias = $('#link_alias').val();
                    // Ensure base_url is defined (it's set in a script tag in HTML head)
                    if (typeof base_url !== 'undefined' && alias) {
                         window.location.href = base_url + 'category/' + alias; // Changed info/ to category/ based on site structure
                    } else {
                         window.location.href = (typeof base_url !== 'undefined' ? base_url : '/') + 'search?keyword=' + encodeURIComponent(keyword);
                    }
                } else {
                     if (typeof base_url !== 'undefined') {
                        window.location.href = base_url + 'search?keyword=' + encodeURIComponent(keyword);
                     } else {
                        window.location.href = '/search?keyword=' + encodeURIComponent(keyword);
                     }
                }
            }

            if ($('#header_search_autocomplete_body:visible').length > 0) {
                let items = $('#header_search_autocomplete_body').children();
                let nextElement = null;
                let current_index = -1;
                let event_id = $("#key_pres").val();

                if (event_id !== '') {
                    if (event_id.substring(0, 'header_search_autocomplete_item_'.length) === 'header_search_autocomplete_item_') {
                        current_index = parseInt(event_id.replace('header_search_autocomplete_item_', ''), 10);
                        $('#header_search_autocomplete_body div').removeClass('focused');
                    }
                }

                if (e.keyCode === 38) { // Up arrow
                    e.preventDefault();
                    current_index = Math.max(0, current_index - 1);
                    nextElement = $('#header_search_autocomplete_item_' + current_index);
                } else if (e.keyCode === 40) { // Down arrow
                    e.preventDefault();
                    current_index = Math.min(items.length - 1, current_index + 1);
                    nextElement = $('#header_search_autocomplete_item_' + current_index);
                }

                if (nextElement && nextElement.length) {
                    nextElement.stop(true, true);
                    // .focus() on a div might not be standard or do what's expected visually.
                    // Typically, focus is for input elements. Adding 'focused' class is good.
                    $('#header_search_autocomplete_item_' + current_index).addClass('focused');
                    $("#key_pres").val('header_search_autocomplete_item_' + current_index);
                    let link_alias = $('#header_search_autocomplete_item_' + current_index + ' a').attr('rel');
                    $("#link_alias").val(link_alias);
                    $("#keyword_search_replace").val(keyword); // This seems to store the original keyword before arrow navigation
                    id = current_index; // Updates the id in the ready function's scope
                }
            }
        });
    });

    // This function is called from within the ready handler, so it's part of its scope if not declared globally.
    // However, to be safe and clear, pass necessary variables or ensure it's defined within the scope that calls it.
    // For now, assuming it's accessible.
    function preload(keyword, id_param) {
        if (typeof base_url_cdn_api === 'undefined') {
            console.error("base_url_cdn_api is not defined. AJAX search will fail.");
            $('.loader').hide();
            return;
        }
        $.ajax({
            url: base_url_cdn_api + "site/loadAjaxSearch", // Ensure this is the correct endpoint
            dataType: 'json',
            data: {
                keyword: keyword,
                id: id_param,
                link_web: (typeof base_url !== 'undefined' ? base_url : '/')
            },
            success: function(data, response) {
                $('.loader').hide();
                $("#header_search_autocomplete").html(data.content);
            },
            error: function() {
                $('.loader').hide();
                $("#header_search_autocomplete").html("<p>Search request failed or no results.</p>");
            }
        });
    }

    // Expose do_search to global scope if it's called by inline HTML onclick attributes
    window.do_search = function() {
        let keyword = $("#search-form #keyword").val();
        if (keyword.length > 2) {
            // Using encodeURIComponent for the keyword for safer URL construction
            window.location.href = (typeof base_url !== 'undefined' ? base_url : '/') + 'search?keyword=' + encodeURIComponent(keyword);
        }
        return false;
    };

})(jQuery);