require(['jquery'], function(jQuery) {
    //TODO: Rewrite the subscriptions as functions to improve readability
    Annotator.Plugin.Storage = function(element) {
        return {
            pluginInit: function() {
                this.annotator.subscribe("beforeAnnotationCreated", function(annotation) {
                        annotation.url = getQueryVariables("id");
                    })
                    .subscribe("annotationCreated", function(annotation) {
                        if (annotation.quote.length > 0) {
                            jQuery.post("./annotator/create.php", JSON.parse(JSON.stringify(annotation)), function(data) {
                                data = JSON.parse(data);
                                annotation.username = data.username;
                                annotation.timecreated = timeConverter(data.timecreated);
                                annotation.id = data.id;
                                annotation.groupname = data.groupname;
                                console.info("The annotation: %o has just been created!", annotation);

                                //Add the annotation to the side pane
                                var text = annotation.text;
                                if (annotation.text.length > 125) {
                                    //Don't display full annotation if its long
                                    text = annotation.text.substring(0, 125) + "...";
                                }
                                var annotation_insert = '<div class="annotation" id="' + annotation.id + '" title="' + annotation.timecreated +
                                    '"><a href="#">';
                                annotation_insert += '<p class="text">' + text + '</p>';
                                annotation_insert += '<p class="username">'
                                if (annotation.groupname) {
                                    annotation_insert += '[' + annotation.groupname + '] ';
                                }
                                annotation_insert += annotation.username + '</p>';
                                annotation_insert += '<hr></a></div>';

                                jQuery(annotation.highlights).attr("data-annotation-id", annotation.id);
                                jQuery(annotation.highlights).attr("id", "annotation_" + annotation.id);
                                jQuery(annotation.highlights).addClass("annotation_" + annotation.id);

                                jQuery('#annotation-list').append(annotation_insert);
                            });
                        }
                    })
                    .subscribe("annotationUpdated", function(annotation) {
                        jQuery.post("./annotator/update.php", JSON.parse(JSON.stringify(annotation)), function(data) {
                            if (data == 0) {
                                //Incorrect user logged in
                                console.error("The annotation couldn't be updated");
                                alert("Warning: You cannot edit annotations created by others!  Any changes you make won't be saved!");
                            } else {
                                annotation.timecreated = timeConverter(data); //Update the time displayed
                                console.info("The annotation: %o has just been updated!", annotation);

                                var annotation_to_update = "#" + annotation.id;
                                jQuery(annotation_to_update).find('.text').text(annotation.text);
                            }
                        });
                    })
                    .subscribe("annotationDeleted", function(annotation) {
                        //Check if the annotation actually exists (workaround annotatorjs bug #258)
                        if (annotation.id) {
                            var post_data = {
                                id: annotation.id
                            };
                            jQuery.post("./annotator/delete.php", post_data, function(data) {
                                if (data == 1) {
                                    console.info("The annotation: %o has just been deleted!", annotation);
                                    var annotation_to_delete = "#" + annotation.id;
                                    jQuery(annotation_to_delete).remove();
                                } else {
                                    console.error("The annotation couldn't be deleted");
                                    alert("Warning: You cannot delete annotations created by others! Any changes you make won't be saved!");
                                }
                            });
                        } else {
                            //Event was called when user clicked cancel. Do nothing. [bug #258]
                        }
                    })
                    /*.subscribe("annotationViewerTextField", function(field, annotation) {
                        field.innerHTML += "<br>";
                        field.innerHTML += "<span style='text-align:right'>" + annotation.username + "</span><br>";
                        field.innerHTML += "<span style='text-align:right'>" + annotation.timecreated + "</span>";
                    }) Use this if the other way takes up too much space*/
                ;
            }
        }
    };

    /**
     * Simple plugin that displays the time an annotation was craeted and the user who created it
     */
    Annotator.Plugin.ExtraData = function(element) {
        var plugin = {};
        plugin.pluginInit = function() {
            this.annotator.viewer.addField({
                load: function(field, annotation) {
                    if (annotation.groupname) {
                        field.innerHTML = annotation.username + " [" + annotation.groupname + "]";
                    } else {
                        field.innerHTML = annotation.username;
                    }
                }
            })
            this.annotator.viewer.addField({
                load: function(field, annotation) {
                    field.innerHTML = annotation.timecreated;
                }
            })
        }
        return plugin;
    }

    //Load the existing annotations when the page is loaded
    jQuery(document).ready(function() {
        var post_data = {
            url: getQueryVariables("id")
        };
        jQuery.post("./annotator/load.php", post_data, function(data) {
            //Load the annotations from the database
            data = JSON.parse(data);
            var editable = data.shift();
            console.log(editable);

            if (editable) {
                var annotator_content = jQuery("#annotator-content").annotator();
            } else {
                var annotator_content = jQuery("#annotator-content").annotator({
                    readOnly: true
                });
            }

            annotator_content.annotator('addPlugin', 'Filter', {
                filters: [{
                    //TODO group support
                    //Only add this filter if group mode && group.visibility
                    label: 'Group',
                    property: 'groupname'
                }, {
                    label: 'User',
                    property: 'username',
                    isFiltered: function(input, username) {
                        if (input && username && username.length) {
                            var keywords = input.split(/\s+/g);
                            username = username.split(" "); //Split first and second name
                            for (var i = 0; i < keywords.length; i += 1) {
                                for (var j = 0; j < username.length; j += 1) {
                                    if (username[j].toUpperCase().indexOf(keywords[i].toUpperCase()) !== -1) { //bad formatting
                                        return true;
                                    }
                                }
                            }
                        }
                        return false;
                    }
                }]
            });

            annotator_content.annotator('addPlugin', 'ExtraData');
            annotator_content.annotator('addPlugin', 'Storage');
            annotator_content.annotator('addPlugin', 'Tags');
            annotator_content.annotator('addPlugin', 'Unsupported');

            for (var i = 0; i < data.length; i++) {
                data[i].text = data[i].annotation;
                delete data[i].annotation;
                data[i].ranges = JSON.parse(data[i].ranges);
                data[i].highlights = JSON.parse(data[i].highlights);
                data[i].tags = JSON.parse(data[i].tags);
                data[i].timecreated = timeConverter(data[i].timecreated);
                console.log(data[i]);
            }

            data.sort(function(a, b) {
                return a.ranges[0].startOffset - b.ranges[0].startOffset;
            });

            //Add annotations to the side panel
            for (var i = 0; i < data.length; i++) {
                var text = data[i].text;
                if (data[i].text.length > 125) {
                    //Don't display full annotation if its long
                    text = data[i].text.substring(0, 125) + "...";
                }
                var annotation_insert = '<div class="annotation" id="' + data[i].id + '" title="' + data[i].timecreated +
                    '"><a href="#">';
                annotation_insert += '<p class="text">' + text + '</p>';
                annotation_insert += '<p class="username">' + data[i].username + '</p>'
                annotation_insert += '<hr></a></div>';
                jQuery('#annotation-list').append(annotation_insert);
            }

            //Load them to the screen[display them as highlights]
            annotator_content.annotator('loadAnnotations', data);

        });

        //Scrolls to the relevant annotation when clicked on
        jQuery('body').on('click', '.annotation', function(e) {
            e.preventDefault();
            var id = this.id;
            var target = "annotation_" + id;
            var position = document.getElementById(target).offsetTop;

            //Check user browser
            if (navigator.userAgent.indexOf("Chrome") != -1) {
                //User is using Chrome
                jQuery(document.body).animate({
                    scrollTop: position
                }, 750);
            } else {
                jQuery('html').animate({
                    scrollTop: position
                }, 750);
            }
        })

        function updateAnnotationList() {
            
        }

        //Highlight the annotation being hovered over
        jQuery('body').on('mouseenter', '.annotation', function(e) {
            var id = this.id;
            var target = "annotation_" + id;
            jQuery('.' + target).toggleClass('annotator-hl-active');
        })

        //Stop annotation the highlighted annotation
        jQuery('body').on('mouseleave', '.annotation', function(e) {
            
            var id = this.id;
            var target = "annotation_" + id;
            jQuery('.' + target).toggleClass('annotator-hl-active');
        })
    });
});

/**
 * Gets a GET variable from the current URL
 */
function getQueryVariables(variable) {
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i = 0; i < vars.length; i++) {
        var pair = vars[i].split("=");
        if (pair[0] == variable) {
            return pair[1];
        }
    }
    return false;
}

/**
 * Converts a UNIX timestamp into readable format
 */
function timeConverter(UNIX_timestamp) {
    var a = new Date(UNIX_timestamp * 1000);
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    var year = a.getFullYear();
    var month = months[a.getMonth()];
    var date = a.getDate();
    var hour = a.getHours();
    if (hour < 10) {
        hour = "0" + hour;
    }
    var min = a.getMinutes();
    if (min < 10) {
        min = "0" + min;
    }
    var time = date + ' ' + month + ' ' + year + ' ' + hour + ':' + min;
    return time;
}
