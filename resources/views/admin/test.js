var _tplAlertMessage =
    '<li class="media innerAll alert-item" alert-url="{%alert_url%}" target="messages">' +
    '<div class="pull-right">' +
    '<span data-original-title="' + trNewAlert + '" data-toggle="tooltip" class="label label-success">New</span>' +
    '</div>' +
    '<div class="media-left">{%user_avatar%}</div>' +
    '<div class="media-body innerlr">' +
    '<a href="{%friend_url%}">{%friend%}</a> ' + trWroteToYou + ' "{%message%}"' +
    '<br>' +
    '<span class="text-caption text-muted timeago" title="{%isotime%}"></span>' +
    '</div>' +
    '</li>';

var _tplAlertGiftMessage =
    '<li class="media innerAll alert-item" alert-url="{%alert_url%}" target="messages">' +
    '<div class="pull-right">' +
    '<span data-original-title="' + trNewAlert + '" data-toggle="tooltip" class="label label-success">New</span>' +
    '</div>' +
    '<div class="media-left">{%user_avatar%}</div>' +
    '<div class="media-body innerlr">' +
    '<a href="{%friend_url%}">{%friend%}</a> ' + trWroteToYou + ' <img src="{%message%}" />' +
    '<br>' +
    '<span class="text-caption text-muted timeago" title="{%isotime%}"></span>' +
    '</div>' +
    '</li>';

var _tplAlertMessagePhoto =
    '<li class="media innerAll alert-item" alert-url="{%alert_url%}" target="messages">' +
    '<div class="pull-right">' +
    '<span data-original-title="' + trNewAlert + '" data-toggle="tooltip" class="label label-success">New</span>' +
    '</div>' +
    '<div class="media-left">{%user_avatar%}</div>' +
    '<div class="media-body innerlr">' +
    '<a href="{%friend_url%}">{%friend%}</a> ' + trSentYouAPhoto +
    '<br>' +
    '<span class="text-caption text-muted timeago" title="{%isotime%}"></span>' +
    '</div>' +
    '</li>';

var _tplAlertMessageAudio =
    '<li class="media innerAll alert-item" alert-url="{%alert_url%}" target="messages">' +
    '<div class="pull-right">' +
    '<span data-original-title="' + trNewAlert + '" data-toggle="tooltip" class="label label-success">New</span>' +
    '</div>' +
    '<div class="media-left">{%user_avatar%}</div>' +
    '<div class="media-body innerlr">' +
    '<a href="{%friend_url%}">{%friend%}</a> ' + trSentYouAudio +
    '<br>' +
    '<span class="text-caption text-muted timeago" title="{%isotime%}"></span>' +
    '</div>' +
    '</li>';

var _tplAlertMessageVideo =
    '<li class="media innerAll alert-item" alert-url="{%alert_url%}" target="messages">' +
    '<div class="pull-right">' +
    '<span data-original-title="' + trNewAlert + '" data-toggle="tooltip" class="label label-success">New</span>' +
    '</div>' +
    '<div class="media-left">{%user_avatar%}</div>' +
    '<div class="media-body innerlr">' +
    '<a href="{%friend_url%}">{%friend%}</a> ' + trSentYouVideo +
    '<br>' +
    '<span class="text-caption text-muted timeago" title="{%isotime%}"></span>' +
    '</div>' +
    '</li>';

var _tplAlertCall =
    '<li class="media innerAll alert-item" alert-url="{%alert_url%}" rel-id="{%rel_id%}" target="messages">' +
    '<div class="pull-right">' +
    '<span data-original-title="' + trNewAlert + '" data-toggle="tooltip" class="label label-success">' + trNew + '</span>' +
    '</div>' +
    '<div class="media-left">{%user_avatar%}</div>' +
    '<div class="media-body innerlr">' +
    '<span class="calling-in-progress"><a href="{%friend_url%}">{%friend%}</a> ' + trIsCallingYou + '</span>' +
    '<span class="calling-closed hidden">' + trMissedCallFrom + '<a href="{%friend_url%}">{%friend2%}</a>.</span>' +
    '<span class="calling-declined hidden"><a href="{%friend_url%}">{%friend2%}</a> ' + trCalledYou + '.</span><div>' +
    '<span class="calling-answered hidden">' + trYouAreInACallWith + '<a href="{%friend_url%}">{%friend2%}</a>.</span><div>' +
    '<div class="btn-call-action-notification innerall">' +
    // '<a href="{%answer_url%}" class="btn btn-success btn-xs instant-load answer-call-from-alert from-alerts" type="{%type%}" target="messages" rel-id="{%rel_id%}">' + trAnswer + '</a><a href="#" class="btn btn-danger btn-xs decline-call from-alerts" rel-id="{%rel_id%}">' + trDecline + '</a></div>' +
    '<span class="text-caption text-muted timeago" title="{%isotime%}"></span>' +
    '</div>' +
    '</li>';

var currentCalls = [];
var $grid = null,
    $filterOptions = null,
    $sizer = null,
    initGrid = null;
var startedTyping = [];
var isInCallWindow = false;
var iconAudioCall = 'microphone';
var iconVideoCall = 'video-camera';
var modalLoadingCall = null;
var callWindowModal = null;
var sendingMessage = false;

var removeCallPreviewClasses = function (element) {
    console.log('mopppppppppp', element);

    var callItem = element.closest('.call-preview-item');
    if (callItem.length) {
        callItem.removeClass('call-preview-item');
        callItem.find('.full-screen-preview').removeClass('full-screen-preview');
        callItem.find('.call-preview-body').removeClass('call-preview-body');
        callItem.find('.call-preview-panel').removeClass('call-preview-panel');
        callItem.find('.caller-preview-container').remove();
        callItem.find('.call-preview-waiting').remove();
        callItem.find('.call-preview-overlay').remove();
        callItem.find('.call-preview-content').removeClass('call-preview-content');
        callItem.find('.call-preview-time').removeClass('call-preview-time');
        callItem.find('.call-preview-avatar-wrapper').removeClass('call-preview-avatar-wrapper');
        callItem.find('.call-preview-avatar-container').removeClass('call-preview-avatar-container');
        callItem.find('.call-preview-info-wrapper').removeClass('call-preview-info-wrapper');
        callItem.find('.call-preview-from').removeClass('call-preview-from');
        callItem.find('.call-preview-direction-status').removeClass('call-preview-direction-status');
        callItem.find('.call-preview-actions').remove();
        callItem.find('.call-preview-type-badge').remove();

        var panel = callItem.find('.panel');
        if (panel.length) {
            panel.removeAttr('style');
            panel.find('.panel-body').css('background', '');
        }
    }
};

$(document).ready(function () {
    if (currentUserId) {
        try {
            initMessages();
        } catch (err) {
            safeNotify(err);
        }
    }

    $('body').on('click', '.answer-call, .decline-call, .ignore-call', function (e) {
        try {
            e.stopPropagation();
            e.preventDefault();

            var _btn = $(this);

            var otsId = _btn.attr('ots-id');
            if (otsId && chatPreviewSessions[otsId]) {
                if (chatPreviewSessions[otsId].subscriber) chatPreviewSessions[otsId].subscriber.destroy();
                if (!_btn.hasClass('answer-call')) {
                    if (chatPreviewSessions[otsId].session) chatPreviewSessions[otsId].session.disconnect();
                    delete chatPreviewSessions[otsId];
                }
            }

            if (_btn.hasClass('answer-call') || _btn.hasClass('decline-call')) {
                removeCallPreviewClasses(_btn);

                var roomId = _btn.attr('rel-id');
                if (_btn.hasClass('answer-call') && typeof callingModals[roomId] != 'undefined' && callingModals[roomId] != null) {
                    callingModals[roomId].data('is-answering', true);
                    _btn.closest('.bootbox.modal').modal('hide');
                }
            }

            var resp = 0;
            if (_btn.hasClass('answer-call')) {
                resp = 1;
            } else if (_btn.hasClass('decline-call') || _btn.hasClass('ignore-call')) {
                resp = -1;
            }

            if (resp == 1) {
                callWindowModal = bootbox.dialog({
                    message: '<div class="text-center" style="padding: 100px;"><i class="fa fa-spin fa-spinner fa-3x text-success"></i><br/><br/><h4>' + trInitiatingCall + '</h4></div>',
                    title: "Call",
                    className: "call-window-bootbox-modal",
                    size: "large",
                    closeButton: true
                });

                //we get the session id and token from the db
                $.ajax({
                    url: answerCallUrl,
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        ots_id: _btn.attr('ots-id'),
                        rel_id: _btn.attr('rel-id'),
                        type: _btn.attr('type')
                    }
                }).done(function (data) {
                    if (data.status == false) {
                        console.log('pppp----------')
                        callWindowModal.modal('hide');
                        callWindowModal = null;
                        bootbox.dialog({
                            message: trChatProblems,
                            title: trChat,
                            buttons: {
                                main: {
                                    label: "OK"
                                }
                            }
                        });

                    } else {
                        //$('.btn-calls').addClass('hidden');
                        //$('.btn-calls.cancel-call').removeClass('hidden');

                        var iframeHtml = '<iframe src="' + callWindowUrl + '?ots_id=' + _btn.attr('ots-id') + '&iframe=true" style="width: 100%; height: 600px; border: none;"></iframe>';
                        callWindowModal.find('.modal-body').html(iframeHtml).css('padding', '0');

                        // Store call details on the modal for use when it closes
                        callWindowModal.data('ots-id', _btn.attr('ots-id'));
                        callWindowModal.data('rel-id', _btn.attr('rel-id'));
                        callWindowModal.data('call-type', _btn.attr('type'));
                        callWindowModal.data('call-connected', false); // Track if call connected

                        callWindowModal.on('hidden.bs.modal', function () {
                            // Only notify server if call hasn't connected yet (user closed before answering)
                            var otsId = $(this).data('ots-id');
                            var relId = $(this).data('rel-id');
                            var callType = $(this).data('call-type');
                            var callConnected = $(this).data('call-connected');

                            // Only send decline if call was closed before being connected/answered
                            if (otsId && relId && !callConnected) {
                                $.ajax({
                                    url: declineCallUrl,
                                    type: 'POST',
                                    dataType: 'JSON',
                                    data: {
                                        ots_id: otsId,
                                        rel_id: relId,
                                        type: callType
                                    }
                                }).done(function (data) {
                                    // Call close notification sent
                                }).fail(function (error) {
                                    // Silently fail
                                });
                            }

                            callWindowModal = null;
                        });
                    }
                }).fail(function (error) {
                    if (callWindowModal) {
                        callWindowModal.modal('hide');
                        callWindowModal = null;
                    }
                    safeNotify(error);
                });
            } else if (resp == -1) {
                $.ajax({
                    url: declineCallUrl,
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        ots_id: _btn.attr('ots-id'),
                        rel_id: _btn.attr('rel-id'),
                        type: _btn.attr('type')
                    }
                }).done(function (data) {
                    if (data.status == false) {
                        bootbox.dialog({
                            message: trChatProblems,
                            title: trChat,
                            buttons: {
                                main: {
                                    label: "OK"
                                }
                            }
                        });
                    } else {

                    }
                }).fail(function (error) {
                    safeNotify(error);
                });
            }
        } catch (sException) {
            safeNotify(sException);

        }
    });
});

var appendAlert = function (payload) {
    try {
        if (payload.from == currentUserId ||
            !(payload.type == 'message' || payload.type == 'call' || payload.type == 'gift' || payload.type == 'call-response' || payload.type == 'call-close' || payload.type == 'photo')) {
            return;
        }

        var currentAlert = prepareAlertTemplate(payload);
        var msg = payload.msg;
        var msgDisplay = '';

        if (payload.type == 'gift') {
            currentAlert = currentAlert.replace('{%message%}', msg);
        }

        if (payload.type == 'message') {
            if (payload.subtype == 'normal') {
                msgDisplay = '';
                if (msg.length > 30) {
                    msgDisplay = msg.substr(0, 30) + '...';
                } else {
                    msgDisplay = msg;
                }
                currentAlert = currentAlert.replace('{%message%}', msgDisplay);
            }
        } else if (payload.type == 'call' || payload.type == 'photo' || payload.type == 'audio' || payload.type == 'chat-video') {

        }

        if (payload.from_typing == false) {
            var numberOfUnreadAlerts = parseInt($('#number-of-unread-alerts').html());

            if (payload.type !== "message") {
                numberOfUnreadAlerts++;
            } else {
                if (isOnChatPage() == true && $('#room-' + payload.room_id).hasClass('active')) {
                    numberOfUnreadAlerts--;
                }
            }

            var totalUnreadMessages = $('#current-number-of-unread-messages').data('total-unread-messages');
            totalUnreadMessages++;
            $('#current-number-of-unread-messages').html(totalUnreadMessages).data('total-unread-messages', totalUnreadMessages).removeClass('hidden');
            $('#number-of-unread-alerts').html(numberOfUnreadAlerts);

            if (numberOfUnreadAlerts > 0) {
                $('#number-of-unread-alerts').removeClass('hidden');
            } else {
                $('#number-of-unread-alerts').addClass('hidden');
            }
            if ($('#alerts-area > .alert-item').length == 5) {
                $('#alerts-area > .alert-item').last().remove();
            }

            $('.there-are-no-alerts-area').remove();

            if (payload.type == 'message' || payload.type == 'gift') {
                playMessageReceivedSound();
            }

            applyAllDocumentReadyEvents();

            $('abbr.timeago:empty, span.timeago:empty').timeago();
        }
    } catch (e) {
        safeNotify(e);
    }
};

var prepareAlertTemplate = function (payload) {
    try {
        var userAvatar = payload.user_avatar_alert;
        var fullName = payload.full_name;
        var msg = payload.msg;
        var env = payload.env;
        var profileUrl = payload.profile_url;
        var alertUrl = payload.chat_url;
        // answerUrl = payload.call_answer_url;
        var roomId = payload.room_id;
        var type = payload.subtype;
        var currentAlert = '';

        if (payload.type == 'message') {
            currentAlert = _tplAlertMessage;
        } else if (payload.type == 'call') {
            currentAlert = _tplAlertCall;
        } else if (payload.type == 'photo') {
            currentAlert = _tplAlertMessagePhoto;
        } else if (payload.type == 'audio') {
            currentAlert = _tplAlertMessageAudio;
        } else if (payload.type == 'chat-video') {
            currentAlert = _tplAlertMessageVideo;
        } else if (payload.type == 'gift') {
            currentAlert = _tplAlertGiftMessage;
        }

        currentAlert = currentAlert.replace('{%user_avatar%}', userAvatar);
        currentAlert = currentAlert.replaceAll('{%friend%}', fullName);
        currentAlert = currentAlert.replaceAll('{%friend2%}', fullName);
        currentAlert = currentAlert.replace('{%friend_url%}', profileUrl);
        currentAlert = currentAlert.replace('{%alert_url%}', alertUrl);
        // currentAlert = currentAlert.replace('{%answer_url%}', answerUrl);
        currentAlert = currentAlert.replaceAll('{%rel_id%}', roomId);
        currentAlert = currentAlert.replace('{%type%}', type);
        var date = new Date();
        currentAlert = currentAlert.replace('{%isotime%}', date.toISOString());

        return currentAlert;
    } catch (e) {
        safeNotify(e);
    }
};

var isOnChatPage = function () {
    try {
        if ($('.chat-window').length || $('.chat-window').length) {
            return true;
        }

        return false;
    } catch (e) {
        safeNotify(e);
    }
};

var initMessages = function () {
    try {
        if ($('#contacts-grid').length) {
            $grid = $('#contacts-grid');
            $filterOptions = $('.grid-filter-options');
            $sizer = $grid.find('.shuffle__sizer');

            initGrid(function () {
                selectFirstContactOrChatFriend();
            });
        }

        if (typeof window.localStorage['reopen_tour_chat_page'] != 'undefined' && window.localStorage['reopen_tour_chat_page'] == 'true') {
            delete (window.localStorage['tour_end']);

            tour.showStep(tourChat);
            tour.init();

            delete (window.localStorage['reopen_tour_chat_page']);
        }
    } catch (e) {
        safeNotify(e);
    }
};

initGrid = function (postRun, reflow) {
    try {
        // None of these need to be executed synchronously
        setTimeout(function () {
            listen();
            setupFilters();
            setupSorting(postRun);
            setupSearching();
        }, 100);

        // You can subscribe to custom events.
        // shrink, shrunk, filter, filtered, sorted, load, done
        $grid.on(
            'loading.shuffle done.shuffle shrink.shuffle shrunk.shuffle filter.shuffle filtered.shuffle sorted.shuffle layout.shuffle',
            function (evt, shuffle) {
                // Make sure the browser has a console
                if (window.console && window.console.log &&
                    typeof window.console.log === 'function') {
                }
            });

        // instantiate the plugin
        $grid.shuffle({
            itemSelector: '.grid-contact-item',
            sizer: $sizer
        });

        // Destroy it! o_O
        if (reflow) {
            $grid.shuffle('destroy');
        }
    } catch (e) {
        safeNotify(e);
    }
};

// Set up button clicks
var setupFilters = function () {

    try {
        var $btns = $filterOptions.find('a');
        $btns.on('click', function (e) {
            e.stopPropagation();
            e.preventDefault();

            var $this = $(this),
                isActive = $this.hasClass('active'),
                group = isActive ? 'all' : $this.attr('data-group');

            // Hide current label, show current label in title
            if (!isActive) {
                $('.grid-filter-options .active').removeClass('active');
            }

            $this.toggleClass('active');

            // Filter elements
            $grid.shuffle('shuffle', group);

            $('.js-shuffle-search').val('');
        });

        $btns = null;
    } catch (e) {
        safeNotify(e);
    }
};

var setupSorting = function (postRun) {
    try {
        if (isOnChatPage()) {
            // Sorting options
            var opts = {
                by: function ($el) {
                    var ret = +(!parseInt($el.attr('data-status'))) + ''
                        - $el.find('.contact-no-messages').html();
                    return ret;
                }
            };

            // Filter elements
            if ($grid != null) {
                $grid.shuffle('sort', opts);

                if (typeof postRun != 'undefined' && typeof postRun === 'function') {
                    postRun();
                }
            }
        }
    } catch (e) {
        safeNotify(e);
    }
};

var setupSearching = function () {
    try {
        // Advanced filtering
        $('.js-shuffle-search').on('keyup change', function () {
            var val = this.value.toLowerCase();
            $grid.shuffle('shuffle', function ($el, shuffle) {

                // Only search elements in the current group
                if (shuffle.group !== 'all' &&
                    $.inArray(shuffle.group, $el.data('groups')) === -1) {
                    return false;
                }

                var _name = $el.data('title');
                var text = $.trim(_name).toLowerCase();
                return text.indexOf(val) !== -1;
            });
        });
    } catch (e) {
        safeNotify(e);
    }
};

var listen = function () {
    try {
        var debouncedLayout = $.throttle(300, function () {
            $grid.shuffle('update');
        });

        // Get all images inside shuffle
        $grid.find('img').each(function () {
            var proxyImage;

            // Image already loaded
            if (this.complete && this.naturalWidth !== undefined) {
                return;
            }

            // If none of the checks above matched, simulate loading on detached element.
            proxyImage = new Image();
            $(proxyImage).on('load', function () {
                $(this).off('load');
                debouncedLayout();
            });

            proxyImage.src = this.src;
        });

        // Because this method doesn't seem to be perfect.
        setTimeout(function () {
            debouncedLayout();
        }, 500);
    } catch (e) {
        safeNotify(e);
    }
};

var _tplBlurredMessage =
    '<div class="media new" id="message_container_{%message_id%}" message-id="{%message_id%}" ts="{%ts%}" style="display: flex; align-items: center; margin-bottom: 15px;">' +
    '<div class="pull-left" style="flex: 1;">' +
    '<div class="media-left">' +
    '<a href="#">' +
    '{%user_avatar%}' +
    '</a>' +
    '</div>' +
    '<div class="media-body message">' +
    '<div class="panel" style="background: none;border: 0;box-shadow: none;min-width: 240px;padding-top: 20px;padding-bottom: 10px;">' +
    '<span class="text-muted pull-right" style="font-size: 0.8rem; color: #bbbbbb;"><abbr class="timeago" title="{%time%}"></abbr></span>' +
    '<div class="panel-body" style="padding: 15px;">' +
    '<div class="message-container" style="text-align: center;">' +
    '<div id="messageCard-{%message_id%}" class="message-card" style="background-color: #0f191d; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); color: #ffffff; width: 300px;">' +
    '<h4 class="message-title" style="color: #21c55d; font-size: 1.2rem; font-weight: bold;">You have a new message!</h4>' +
    '<p class="message-text" style="font-size: 0.9rem; color: #bbbbbb; margin: 10px 0;">The message will be deleted if it is not read within the time displayed below.</p>' +
    '<div id="countdown-{%message_id%}" class="countdown-timer" style="font-size: 1.5rem; font-weight: bold; color: #21c55d; margin: 10px 0;">' +
    '<i class="fa fa-clock" style="color: #21c55d"></i>{%countdownTime%}</div>' +
    '<button class="upgrade-btn" style="background-color: #21c55d; border: none; padding: 10px; font-size: 1rem; color: #000; font-weight: bold; cursor: pointer; border-radius: 5px; width: 100%;">Upgrade to read</button>' +
    '</div>' +
    '</div>' +
    '</div>' +
    '</div>' +
    '</div>' +
    '</div>' +
    '</div>';

var _tplMessage =
    '<div class="media new" message-id="{%message_id%}" ts="{%ts%}">' +
    '<div class="pull-{%direction%}">' +
    '{%media_left%}' +
    '<div class="media-body message">' +
    '<div class="panel">' +
    '<span class="text-muted pull-right"><abbr class="timeago" title="{%time%}"></abbr></span>' +
    '<div class="panel-body">' +
    '{%message%}' +
    '</div>' +
    '</div>' +
    '</div>' +
    '{%media_right%}' +
    '</div>' +
    '</div>';

var _tplGiftMessage =
    '<div class="media new" message-id="{%message_id%}" ts="{%ts%}">' +
    '<div class="pull-{%direction%}">' +
    '{%media_left%}' +
    '<div class="media-body message">' +
    '<div class="panel">' +
    '<span class="text-muted pull-right"><abbr class="timeago" title="{%time%}"></abbr></span>' +
    '<div class="panel-body"><div class="image-upload-message"><img src="' +
    '{%message%}' + '" style="width: 100%;max-width: 200px;height: 100%;max-height: 200px;" />' +
    '</div></div>' +
    '</div>' +
    '</div>' +
    '{%media_right%}' +
    '</div>' +
    '</div>';

var _tplCall =
    '<div class="media call new call-preview-item" message-id="{%message_id%}" ts="{%ts%}" ots-id="{%ots_id%}">' +
    '<div class="pull-{%direction%}">' +
    '{%media_left%}' +
    '<div class="media-body message call-preview-body">' +
    '<div class="panel panel-default call-preview-panel">' +

    // Video Preview Layer (Full Container)
    '<div id="caller-preview-{%message_id%}" class="caller-preview-container">' +
    '<div class="call-preview-waiting">' +
    '<i class="fa fa-video-camera fa-3x" style="display:block; margin-bottom:10px; opacity:0.3;"></i>' +
    '</div>' +
    '</div>' +
    '<div class="call-preview-overlay"></div>' +

    // Content Layer
    '<div class="panel-body call-preview-content">' +
    '<span class="text-muted call-preview-time"><abbr class="timeago" title="{%time%}"></abbr></span>' +
    '<div class="row" style="margin: 0;">' +
    '<div class="col-md-12 center">' +
    '<div class="call-preview-avatar-wrapper">' +
    '<div class="call-preview-avatar-container">' +
    '{%user_avatar%}' +
    '</div>' +
    '</div>' +
    '</div>' +
    '<div class="col-md-12 center">' +
    '<div class="call-preview-info-wrapper">' +
    '<h1 class="call-preview-from">{%from%}</h1>' +
    '<div class="call-preview-direction-status">' +
    '{%call_direction2%}' +
    '</div>' +
    '</div>' +
    '<div class="call-preview-actions">' +
    '{%call_direction%}' +
    '</div>' +
    '</div>' +
    '</div>' +
    '</div>' +
    '</div>' +
    '</div>' +
    '{%media_right%}' +
    '</div>' +
    '</div>';

var _tplMedia =
    '<div class="media-{%direction%}">' +
    '<a href="#">' +
    '{%user_avatar%}' +
    '</a>' +
    '</div>';

var _tplCallLeft =
    '<a href="#" class="btn btn-call-answer-premium answer-call" rel-id="{%rel_id%}" type="{%type%}" ots-id="{%ots_id%}"><i class="fa fa-phone" style="margin-right: 12px;"></i> ' + trAnswer + '</a>' +
    '<a href="#" class="btn btn-call-decline-premium decline-call" rel-id="{%rel_id%}" ots-id="{%ots_id%}"><i class="fa fa-times-circle" style="margin-right: 12px;"></i> ' + trDecline + '</a>';

var _tplCallRight = '<a href="#" class="btn btn-call-stop-premium decline-call" rel-id="{%rel_id%}" ots-id="{%ots_id%}"><i class="fa fa-stop-circle" style="margin-right: 10px;"></i> ' + trStop + '</a>';

var _tplCallLeft2 = '<div class="call-preview-type-badge incoming">is {%call_type%} calling you</div>';

var _tplCallRight2 = '<div class="call-preview-type-badge outgoing">{%call_type%} calling</div>';

var _tplPhotoChat = '<div class="image-upload-message"><a href="{%photo_url%}">' +
    // '<i class="fa fa-file-image-o" aria-hidden="true"></i>' +
    // '<span class="zoom-image-upload-message">' +
    // '<i class="fa fa-eye" aria-hidden="true"></i>' +
    // '</span>' +
    '<img class="img-responsive" src="data:image/jpg;base64,{%encode%}" />' +
    '</a></div>';

var _tplActiveAudioChat = '<div class="{%class%}" data-href="{%active_audio_url%}" data-message-id="{%message_id%}">' +
    '<audio {%oncontextmenu%} controls data-message-id="{%message_id%}" data-href="{%active_audio_url%}" class="audio-{%message_id%} audio-message-source {%active_class%}" src="{%get_audio_message_url%}" type="audio/aac">' +
    '</audio>' +
    '</div>';

var _tplInactiveAudioChat = '<div class="inactive-audio" data-href="{%inactive_audio_url%}" data-message-id="{%message_id%}">' +
    '<a href="javascript:;"><i class="fa fa-solid fa-play"></i> <span class="inactive-click-listen">' + clickToListen + '</span></a>' +
    '<audio data-message-id="{%message_id%}" controls class="inactive-audio-message-source {%class%}" src="#" type="audio/aac">' +
    '</audio>' +
    '</div>';

var _tplActiveVideoChat = '<div class="{%class%}" data-href="{%active_video_url%}" data-message-id="{%message_id%}">' +
    '<figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">' +
    '<video {%oncontextmenu%} width="100%" height="100%" controls data-message-id="{%message_id%}" data-href="{%active_video_url%}" class="video-{%message_id%} {%active_class%}">' +
    '<source class="video-message-source" src="{%get_video_message_url%}" type="video/mp4"> Your browser does not support the video element.' +
    '</video>' +
    '</figure>' +
    '</div>';

var _tplInactiveVideoChat = '<div class="inactive-chat-video" data-href="{%inactive_video_url%}" data-message-id="{%message_id%}">' +
    '<figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">' +
    '<video width="100% "height="100%" data-message-id="{%message_id%}" controls class="{%class%}">' +
    '<source class="inactive-video-message-source" src="#" type="video/mp4"> Your browser does not support the video element.' +
    '</video>' +
    '</figure>' +
    '<button class="btn btn-sm btn-success blurred-video-message-play"><i class="fa fa-solid fa-play"></i>' + clickToPlay + '</button>' +
    '</div>';

var currentRelId = null;
var delay = 500;
var callClosedDelay = 20000;

var processingMessagesQueue = [];

var sortedContacts = [];

$(document).on('click', '.not-allowed-audio-btn', function () {
    let message = $('.chat-audio-div').attr('title');
    bootbox.alert(message);
})


var redirectToChat = function () {
    if (typeof isInIframe !== 'undefined' && isInIframe) {
        if (typeof window.parent.$ !== 'undefined') {
            var modal = window.parent.$('.call-window-bootbox-modal');
            console.log('1111')
            modal.modal('hide');
        }
    } else {
        window.location = $('.redirect-to-chat-btn').attr('href');
    }
};

$(document).ready(function () {
    $('body').on('click', '.contact-list-item:not(.ignore-from-other-events)', function (e) {
        try {
            e.stopPropagation();
            e.preventDefault();

            var _btnContact = $(this);
            var relId = _btnContact.attr('relationship-id');
            var contactName = _btnContact.attr('contact-name').escape();
            var lastMessage = _btnContact.find('.last-message').html();
            var profileUrl = _btnContact.attr('contact-profile-url');
            var photosUrl = _btnContact.attr('contact-photos-url');
            var chatPhotosUrl = _btnContact.attr('contact-chat-photos-url');
            var statusObj = _btnContact.find('span.status');
            var status = statusObj.hasClass('status-offline')
                ? 'offline'
                : (statusObj.hasClass('status-online') ? 'online' : 'away');
            var nbrPhotos = _btnContact.attr('nbr-photos');
            var fromUserContactId = _btnContact.attr('user-contact-id');

            $('.room-area').addClass('hidden');
            $('.room-area').removeClass('active');
            $('#room-' + relId).removeClass('hidden');
            $('#room-' + relId).addClass('active');

            currentRelId = relId;

            let notAllowedPackagesForAudio = notDisplayAudioButtonPackages.split(", ");
            let notAllowedPackagesForVideo = notDisplayVideoButtonPackages.split(", ");

            $.ajax({
                url: allMessagesMarkAsRealReadUrl,
                data: { 'relationshipId': relId, 'fromUserContactId': fromUserContactId }
            }).done(function (data) {
                if (notAllowedPackagesForAudio.includes(data.fromUserPackage)) {
                    $('#add-chat-audio-btn').removeClass('btn-add-audio-chat')
                    $('#add-chat-audio-btn').parents('.chat-audio-div').addClass('not-allowed-audio-btn');
                    $('#add-chat-audio-btn').attr('disabled', true);
                    $('.chat-audio-div').attr('data-toggle', 'tooltip');
                    let title = notAllowedAudioButtonMsg.replace("%username%", data.fromUserName).replace("%not_allowed_packages%", notDisplayAudioButtonPackages)
                    $('.chat-audio-div').attr('title', title);
                } else {
                    $('#add-chat-audio-btn').addClass('btn-add-audio-chat')
                    $('#add-chat-audio-btn').parents('.chat-audio-div').removeClass('not-allowed-audio-btn');
                    $('#add-chat-audio-btn').removeAttr('disabled');
                    $('.chat-audio-div').removeAttr('data-toggle');
                    $('.chat-audio-div').removeAttr('title');
                }

                if (notAllowedPackagesForVideo.includes(data.fromUserPackage)) {
                    $('#add-chat-video-btn').removeClass('btn-add-video-chat')
                    $('#add-chat-video-btn').parents('.chat-video-div').addClass('not-allowed-video-btn');
                    $('#add-chat-video-btn').attr('disabled', true);
                    $('.chat-video-div').attr('data-toggle', 'tooltip');
                    let title = notAllowedVideoButtonMsg.replace("%username%", data.fromUserName).replace("%not_allowed_packages%", notDisplayVideoButtonPackages)
                    $('.chat-video-div').attr('title', title);
                } else {
                    $('#add-chat-video-btn').addClass('btn-add-video-chat')
                    $('#add-chat-video-btn').parents('.chat-video-div').removeClass('not-allowed-video-btn');
                    $('#add-chat-video-btn').removeAttr('disabled');
                    $('.chat-video-div').removeAttr('data-toggle');
                    $('.chat-video-div').removeAttr('title');
                }

                var count = parseInt($('#number-of-unread-alerts').html()) - parseInt($('#room-' + relId).find($('.contact-no-messages')).html());

                if (count > 0) {
                    $('#number-of-unread-alerts').html(count)
                } else {
                    $('#number-of-unread-alerts').addClass('hidden');
                }
            });

            $('#chat-avatar-area').html('');
            $('#chat-contact').html('');
            $('#chat-last-message').html('');
            $('#chat-profile-url').attr('href', '');
            $('#chat-photos-url').attr('href', '');
            $('#chat-pictures').data('url', '');
            if (nbrPhotos > 0) {
                $('.photos-link').removeClass('hidden');
            } else {
                $('.photos-link').addClass('hidden');
            }
            $('#chat-status').removeClass('status-online').removeClass('status-offline').removeClass('status-away').data('original-title', status.capitalizeFirstLetter()).attr('data-original-title', status.capitalizeFirstLetter());

            $('.chat-avatar.rel-' + relId).clone().removeClass('hidden').removeClass('rel-' + relId).appendTo('#chat-avatar-area');
            $('#chat-contact').html(contactName);
            $('#chat-last-message').html(lastMessage);
            $('#chat-profile-url').attr('href', profileUrl).find('span').html(contactName + ' ' + trProfile);
            $('#chat-photos-url').attr('href', photosUrl).find('span').html(contactName + ' ' + trPhotos);
            $('#chat-status').addClass('status-' + status);
            $('#chat-pictures').data('url', chatPhotosUrl);
            $("#delete-conversation-btn").attr('rel-id', relId)

            $('.btn-calls').attr('rel-id', relId);
            if (status == 'online') {
                switchButtonsToOnline();
            } else {
                switchButtonsToOffline();
            }

            if ($.inArray(parseInt(currentRelId), currentCalls) != -1) {
                switchButtonsToCall();
            }
            // if (typeof openTokData[currentRelId] != 'undefined' && (openTokData[currentRelId]['currentState'] == 'connected' || openTokData[currentRelId]['currentState'] == 'calling')) {
            //     switchButtonsToCall();
            //     if (openTokData[currentRelId]['currentState'] == 'connected') {
            //         $('.widget.chat-window').addClass('chat-window-open');
            //     }
            // } else {
            //     $('.widget.chat-window').removeClass('chat-window-open');
            // }

            $('.video.row').addClass('hidden');
            $('.video.row[rel-id="room-' + relId + '"]').removeClass('hidden');

            var _loadMoreBtn = $('#room-' + relId + ' .load-more-chats-btn');
            if (_loadMoreBtn.attr('page') == 1) {
                _loadMoreBtn.trigger('click');
            }

            var noMessagesArea = _btnContact.find('.contact-no-messages');
            noMessagesArea.html('0').addClass('hidden');
            updateNumberOfUnreadMessagesByUser();
            updateTotalUnreadMessages();

            $('#chat-input').trigger('focus');
        } catch (sException) {
            safeNotify(sException);

        }
    });

    $('body').on('click', '.load-more-chats-btn', function (e) {
        try {
            e.stopPropagation();
            e.preventDefault();

            var _btn = $(this);

            if ($.inArray(_btn.attr('room-id'), processingMessagesQueue) == -1) {
                processingMessagesQueue.push(_btn.attr('room-id'));

                _btn.append('<i class="fa fa-spin fa-spinner"></i>');
                $('.room-area#room-' + _btn.attr('room-id') +
                    ' .history-media').after(
                        '<div class="center with-spinner"><i class="fa fa-spin fa-spinner fa-2x"></i></div>');

                var newMessageIds = [];
                var newMessagesNodes = $(
                    '.room-area#room-' + _btn.attr('room-id') + ' .media.new');
                $.each(newMessagesNodes, function (pos, elem) {
                    newMessageIds.push($(elem).attr('message-id'));
                });

                $.ajax({
                    url: _btn.attr('load-more-url'),
                    type: 'POST',
                    data: {
                        page: _btn.attr('page'),
                        new_message_ids: newMessageIds
                    },
                    dataType: 'JSON'
                }).done(function (data) {
                    $('.send-audio-modal-content').html(sendAudioModalHeaderContent)
                    $('.send-chat-video-modal-content').html(sendVideoMessageModalHeaderContent)
                    let contentHtml = $('.send-audio-modal-content').html().replace('%toUser%', "<p class='toUsername'>" + data.to_username + "</p>").replace('%username%', "<p class='fromUsername'>" + data.from_username + "</p>")
                    $('.send-audio-modal-content').html(contentHtml);
                    let videoMessageContentHtml = $('.send-chat-video-modal-content').html().replace('%toUser%', data.to_username).replace('%username%', data.from_username)
                    $('.send-chat-video-modal-content').html(videoMessageContentHtml);

                    var _messagesArea = $('.room-area#room-' + data.relationship_id);

                    _messagesArea.find('.center.with-spinner').remove();
                    $('.room-area#room-' + data.relationship_id).find('.history-media').after(data.content);

                    scrollToBottomOfRoomChat(data.relationship_id);

                    checkMessageVisibility(_messagesArea);

                    var _messageItemArea = _messagesArea.find(
                        '.media[message-id="' + data.last_message_id + '"]');
                    if (typeof _messageItemArea.position() != 'undefined') {
                        _messagesArea.scrollTop(_messagesArea.scrollTop() +
                            _messageItemArea.position().top);

                        $('abbr.timeago:empty').timeago();
                        $('.timer').startTimer({
                            onComplete: function (element) {
                                $(this).remove();
                                element.parents('.received-video').find('.timer-div').remove()
                            }
                        });
                        $('.video-message-timer').startTimer({
                            onComplete: function (element) {
                                element.parents('.received-video').find('.video-timer-div').remove()
                            }
                        });

                        var _btnRet = $('.load-more-chats-btn[room-id="' + data.relationship_id + '"]');
                        _btnRet.attr('page',
                            parseInt(_btnRet.attr('page')) + 1);

                        if (data.load_more == false) {
                            _btnRet.remove();
                        }

                        _btnRet.find('i').remove();

                        _messagesArea.scroll(function (e) {
                            e.stopPropagation();
                            e.preventDefault();

                            checkMessageVisibility(_messagesArea);

                            var _lastVisibleMessage = _messagesArea.find(
                                '.media.visible:last');
                            var _ts = _lastVisibleMessage.attr('ts');

                            var _messageDate = new Date(_ts * 1000);

                            var months = [
                                'Jan',
                                'Feb',
                                'Mar',
                                'Apr',
                                'May',
                                'Jun',
                                'Jul',
                                'Aug',
                                'Sep',
                                'Oct',
                                'Nov',
                                'Dec'];
                            var year = _messageDate.getFullYear();
                            var month = months[_messageDate.getMonth()];
                            var date = _messageDate.getDate();
                            var hour = _messageDate.getHours();
                            var min = _messageDate.getMinutes();
                            var sec = _messageDate.getSeconds();
                            var time = date + ' ' + month + ' ' + year + ' ' +
                                hour + ':' + min + ':' + sec;

                            //_messagesArea.find('.message-time').html(time);

                            // var pos = _messagesArea.scrollTop();
                            // if (pos == 0) {
                            //     _messagesArea.find('.load-more-chats-btn').trigger('click');
                            // }
                            _messagesArea.find('#load-' + data.relationship_id).click(function () {
                                var rel = $(this).attr('rel');
                                $('#room-' + rel + ' .load-more-chats-btn').trigger('click');
                                $(this).hide()
                            });
                        });

                        processingMessagesQueue = unsetArrayElementByValue(
                            processingMessagesQueue, data.relationship_id);
                    }

                    _messagesArea.find('.active-audio-message').on('play', function () {
                        applyPlay(e, $(this));
                    })

                    _messagesArea.find('.active-video-message').on('play', function () {
                        applyChatVideoPlay(e, $(this));
                    })

                    _messagesArea.find('.active-audio-message').on('pause', function () {
                        applyPause(e, $(this));
                    })

                    _messagesArea.find('.active-audio-message').bind('contextmenu', function (e) {
                        e.preventDefault();
                    });

                    _messagesArea.find('.active-video-message').bind('contextmenu', function (e) {
                        e.preventDefault();
                    });

                }).fail(function (error) {
                    safeNotify(error);
                });
            }
        } catch (sException) {
            safeNotify(sException);

        }
    });

    $('body').on('click', '.btn-calls:not(.cancel-call)', function (e) {
        try {
            e.stopPropagation();
            e.preventDefault();

            var _btn = $(this);
            // $('.btn-calls').addClass('hidden');

            callWindowModal = bootbox.dialog({
                message: '<div class="text-center" style="padding: 100px;"><i class="fa fa-spin fa-spinner fa-3x text-success"></i><br/><br/><h4>' + trInitiatingCall + '</h4></div>',
                title: "Call",
                className: "call-window-bootbox-modal",
                size: "large",
                closeButton: true
            });

            callWindowModal.find('.bootbox-close-button').addClass('decline-call bootbox-decline-call-button').removeClass('bootbox-close-button');

            $.ajax({
                url: _btn.attr('href'),
                type: 'POST',
                dataType: 'JSON',
                data: { rel_id: _btn.attr('rel-id') }
            }).done(function (data) {
                if (data.error_flag == true) {
                    if (callWindowModal) {
                        console.log('2222')
                        callWindowModal.modal('hide');
                        callWindowModal = null;
                    }
                    SocApp.displayFlashMessage(data.message);
                    //$('.btn-calls').removeClass('hidden');
                    return;
                }

                if (data.permission == 'granted') {
                    var relId = data.relationship_id;
                    $(".call-window-bootbox-modal .decline-call.bootbox-decline-call-button").attr({ 'ots-id': data.ots_id, 'rel-id': data.relationship_id, 'call-type': data.type, "aria-hidden": "true" });

                    playCallingSound();

                    var iframeUrl = callWindowUrl + '?ots_id=' + data.ots_id + '&iframe=true';
                    var iframeHtml = '<iframe src="' + iframeUrl + '" style="width: 100%; height: 600px; border: none;"></iframe>';

                    if (callWindowModal) {
                        callWindowModal.find('.modal-body').html(iframeHtml).css('padding', '0');
                        callWindowModal.on('hidden.bs.modal', function () {
                            callWindowModal = null;
                        });
                    }
                    return;
                }

                if (data.show_modal == true) {
                    if (callWindowModal) {
                        console.log('33333')
                        callWindowModal.modal('hide');
                        callWindowModal = null;
                    }
                    closeAllCallingModals(); // Close any calling modals first
                    bootbox.hideAll();
                    showPaymentModal(data.type, null, null, null, data.message);
                    //$('.btn-calls').removeClass('hidden');
                }
            }).fail(function (error) {
                if (callWindowModal) {
                    console.log('44444')
                    callWindowModal.modal('hide');
                    callWindowModal = null;
                }
                safeNotify(error);
                //$('.btn-calls').removeClass('hidden');
            });
        } catch (sException) {
            safeNotify(sException);
        }
    });

    $('body').on('click', '.hang-up-call', function (e) {
        try {
            e.stopPropagation();
            e.preventDefault();

            var _btn = $(this);

            relId = _btn.attr('rel-id');
            otsId = _btn.attr('ots-id');

            hangUpCall(relId, otsId);

            $('.widget.chat-window').removeClass('chat-window-open');

            //$('.btn-calls.cancel-call').addClass('hidden');

            if (callWindowModal) {
                console.log('55555')
                callWindowModal.modal('hide');
                callWindowModal = null;
            }

            // Close the modal if in iframe
            if (typeof isInCallWindow !== 'undefined' && isInCallWindow && typeof isInIframe !== 'undefined' && isInIframe) {
                setTimeout(function () {
                    redirectToChat();
                }, 1000);
            }
        } catch (sException) {
            safeNotify(sException);

        }
    });

    $('body').on('click', '.image-upload-message', function (e) {
        try {
            e.stopPropagation();
            e.preventDefault();

            var _link = $(this).find('a');
            modalLoadingCall = bootbox.dialog({
                message: '<i class="fa fa-spin fa-spinner"></i> ' +
                    trInitiatingCall,
                title: trLoading
            });
            $.ajax({
                url: _link.attr('href'),
                type: 'POST'
            }).done(function (data) {
                console.log('66666')
                modalLoadingCall.modal('hide');
                modalLoadingCall = null;
                if (data.error_flag == false) {
                    if (data.permission == 'granted') {
                        $('.number-active-credits').html(data.user_data.credits);
                        $('.amount-credits').html(data.user_data.credits);
                        SocApp.updateCreditButtons(data.user_data);
                        bootbox.dialog({
                            message: '<img src="data:image/png;base64,' + data.message +
                                '" class="img-responsive"/>',
                            title: trChat,
                            buttons: {
                                main: {
                                    label: "OK"
                                }
                            }
                        });


                        $('.bootbox.modal').addClass('view-image-upload-message');
                        $('.bootbox-body ').contextmenu(function () {
                            return false;
                        });
                    } else {
                        if (data.show_modal) {
                            console.log('77777')
                            closeAllCallingModals(); // Close any calling modals first
                            bootbox.hideAll();
                            showPaymentModal(null, null, null, null, data.message);
                        } else {
                            bootbox.dialog({
                                message: data.message,
                                title: trChat,
                                buttons: {
                                    main: {
                                        label: "OK"
                                    }
                                }
                            });
                        }
                    }
                } else {
                    bootbox.dialog({
                        message: data.message,
                        title: trChat,
                        buttons: {
                            main: {
                                label: "OK"
                            }
                        }
                    });
                }
            }).fail(function (jqXHR) {
                console.log('88888')
                modalLoadingCall.modal('hide');
                modalLoadingCall = null;
                if (jqXHR.status == 204) {
                    bootbox.dialog({
                        message: jqXHR.responseText,
                        title: trChat,
                        buttons: {
                            main: {
                                label: "OK"
                            }
                        }
                    });
                }
            });
        } catch (sException) {
            console.log('99999')
            modalLoadingCall.modal('hide');
            modalLoadingCall = null;
            safeNotify(sException);

        }
    });

    $('body').on('click', '#chat-pictures', function (e) {
        try {
            $('#page-loader-overlay').removeClass('hidden');
            e.stopPropagation();
            e.preventDefault();

            $.ajax({
                url: $(this).data('url'),
                type: 'GET'
            }).done(function (data) {
                $('#page-loader-overlay').addClass('hidden');
                let username = $('#chat-contact').text();
                bootbox.dialog({
                    message: data.content,
                    title: `${trPictures} ${username}`,
                    buttons: {
                        main: {
                            label: "Close",
                            callback: function () {
                                setTimeout(function () {
                                    $('.bootbox.modal').removeClass('chat-pictures-modal');
                                }, 300);
                            }
                        }
                    },
                    onEscape: function () {
                        setTimeout(function () {
                            $('.bootbox.modal').removeClass('chat-pictures-modal');
                        }, 300);
                    }
                });
                $('.bootbox.modal').addClass('chat-pictures-modal');
                $('.bootbox-body ').contextmenu(function () {
                    return false;
                });

                $('abbr.timeago:empty, span.timeago:empty').timeago();
                setTimeout(function () {
                    $('.row.masonry-grid').masonry({
                        percentPosition: true
                    });
                }, 300);
            }).fail(function (error) {
                $('#page-loader-overlay').addClass('hidden');
                safeNotify(error);
            });
        } catch (sException) {
            $('#page-loader-overlay').addClass('hidden');
            safeNotify(sException);

        }
    });

    $(document).on({
        mouseenter: function () {
            $(this).find('img').css("opacity", "0.4");
            $(this).find('.post-content').removeClass('d-none');
        },
        mouseleave: function () {
            $(this).find('img').css("opacity", "1");
            $(this).find('.post-content').addClass('d-none');
        }
    }, '.chat-pictures-modal .image-upload-message');

    $(document).on('click', '.inactive-chat-video', function (e) {
        playInactiveMessageVideo(e, $(this));
    })

    $(document).on('click', '.inactive-audio', function (e) {
        playInactiveAudio(e, $(this));
    })

    function playInactiveMessageVideo(e, obj) {
        try {
            e.stopPropagation();
            e.preventDefault();

            var _link = $(obj).data('href');

            $.ajax({
                url: _link,
                type: 'POST'
            }).done(function (data) {
                if (data.error_flag == false) {
                    if (data.permission !== 'granted' && typeof data.upgradableModalRaise != "undefined" && data.upgradableModalRaise == true) {
                        upgradableModal('Upgrade package', $('#upgradePackageModal'), data.upgradable_message);
                        return;
                    }
                    if (data.permission == 'granted') {
                        $('.number-active-credits').html(data.user_data.credits);
                        $('.amount-credits').html(data.user_data.credits);
                        SocApp.updateCreditButtons(data.user_data);

                        obj.find('.inactive-audio-message-source').attr('src', data.message)
                    } else {
                        if (data.show_modal) {
                            showPaymentModal(null, null, null, null, data.message);
                        } else {
                            bootbox.dialog({
                                message: data.message,
                                title: trChat,
                                buttons: {
                                    main: {
                                        label: "OK"
                                    }
                                }
                            });
                        }
                    }
                } else {
                    bootbox.dialog({
                        message: data.message,
                        title: trChat,
                        buttons: {
                            main: {
                                label: "OK"
                            }
                        }
                    });
                }
            }).fail(function (jqXHR) {
                if (jqXHR.status == 204) {
                    bootbox.dialog({
                        message: jqXHR.responseText,
                        title: trChat,
                        buttons: {
                            main: {
                                label: "OK"
                            }
                        }
                    });
                }
            });
        } catch (sException) {
            safeNotify(sException);

        }
    }

    function playInactiveAudio(e, obj) {
        try {
            e.stopPropagation();
            e.preventDefault();

            var _link = $(obj).data('href');

            $.ajax({
                url: _link,
                type: 'POST'
            }).done(function (data) {
                if (data.error_flag == false) {
                    if (data.permission !== 'granted' && typeof data.upgradableModalRaise != "undefined" && data.upgradableModalRaise == true) {
                        upgradableModal('Upgrade package', $('#upgradePackageModal'), data.upgradable_message);
                        return;
                    }
                    if (data.permission == 'granted') {
                        $('.number-active-credits').html(data.user_data.credits);
                        $('.amount-credits').html(data.user_data.credits);
                        SocApp.updateCreditButtons(data.user_data);

                        obj.find('.inactive-audio-message-source').attr('src', data.message)
                    } else {
                        if (data.show_modal) {
                            showPaymentModal(null, null, null, null, data.message);
                        } else {
                            bootbox.dialog({
                                message: data.message,
                                title: trChat,
                                buttons: {
                                    main: {
                                        label: "OK"
                                    }
                                }
                            });
                        }
                    }
                } else {
                    bootbox.dialog({
                        message: data.message,
                        title: trChat,
                        buttons: {
                            main: {
                                label: "OK"
                            }
                        }
                    });
                }
            }).fail(function (jqXHR) {
                if (jqXHR.status == 204) {
                    bootbox.dialog({
                        message: jqXHR.responseText,
                        title: trChat,
                        buttons: {
                            main: {
                                label: "OK"
                            }
                        }
                    });
                }
            });
        } catch (sException) {
            safeNotify(sException);

        }
    }

    try {
        bindUi();
    } catch (sException) {
        safeNotify(sException);

    }
});

function publishChat(subtype) {
    try {
        var msg = $("#chat-input").val();

        if (msg.trim() != '') {
            $("#chat-input").val('');

            addPayload(currentRelId, { subtype: subtype, msg: msg, type: 'message' });
        }
    } catch (e) {
        safeNotify(e);
    }
}

function appendChat(payload) {
    console.log('payload000000000', payload.subtype)
    if (typeof isInCallWindow !== 'undefined' && isInCallWindow) {
        // Allow messages to be displayed in call window chat
        if (payload.type == 'message' || payload.type == 'photo' || payload.type == 'audio' || payload.type == 'chat-video' || payload.type == 'gift') {
            if (payload.subtype == 'normal') {
                appendChatMessage(payload);
            }
        }
        return;
    }
    try {
        var roomId = 0;
        if (payload.type == 'message' || payload.type == 'photo' || payload.type == 'audio' || payload.type == 'chat-video' || payload.type == 'gift') {
            if (payload.subtype == 'normal') {
                appendChatMessage(payload);
            }

        } else if (payload.type == 'call') {
            appendChatMessage(payload);

        } else if (payload.type == 'call-response') {
            console.log('i am in cal reponse')
            roomId = payload.room_id;
            var mediaMessage = $(
                '#room-' + roomId + ' .media.call[ots-id="' + payload.ots_id +
                '"] .panel-body');

            if (payload.subtype == 'accept') {
                if (chatPreviewSessions[payload.ots_id]) {
                    if (chatPreviewSessions[payload.ots_id].subscriber) chatPreviewSessions[payload.ots_id].subscriber.destroy();
                    // if (chatPreviewSessions[payload.ots_id].session) chatPreviewSessions[payload.ots_id].session.disconnect();
                    // delete chatPreviewSessions[payload.ots_id];
                }
                // mediaMessage.html(trAnswered + '.<a href="#" class="btn btn-danger btn-xs hang-up-call" rel-id="' + roomId + '" ots-id="' + payload.ots_id + '">' + trHangUp + '</a>');
                mediaMessage.html('<div class="row"><i class="fa fa-' +
                    ((payload.method == 'audio')
                        ? iconAudioCall
                        : iconVideoCall) + ' call-answered"></i> ' +
                    trCallAnswered +
                    '</div><div class="row"><a href="#" class="btn btn-danger btn-xs hang-up-call" rel-id="' +
                    roomId + '" ots-id="' + payload.ots_id +
                    '"><i class="fa fa-times-circle"></i> ' + trHangUp +
                    '</a></div>');

                removeCallPreviewClasses(mediaMessage);

                // Close the calling modal (receiver's popup) when call is accepted
                if (typeof callingModals[payload.room_id] != 'undefined' &&
                    callingModals[payload.room_id] != null) {
                    closeCallingModal(payload.room_id);
                }

                if (payload.from == currentUserId) {
                    // I am the initiator, the modal is already open and handled via iframe internal logic
                    // Mark the call as connected so we don't send decline notification on close
                    if (callWindowModal) {
                        callWindowModal.data('call-connected', true);
                    }
                } else {
                    if (callWindowModal) {
                        return;
                    }

                    var iframeUrl = callWindowUrl + '?ots_id=' + payload.ots_id + '&iframe=true';
                    var iframeHtml = '<iframe src="' + iframeUrl + '" style="width: 100%; height: 600px; border: none;"></iframe>';

                    callWindowModal = bootbox.dialog({
                        message: iframeHtml,
                        title: "Call",
                        className: "call-window-bootbox-modal",
                        size: "large",
                        closeButton: true
                    });

                    callWindowModal.find('.modal-body').css('padding', '0');
                    callWindowModal.on('hidden.bs.modal', function () {
                        callWindowModal = null;
                    });
                }
            } else if (payload.subtype == 'decline') {
                if (chatPreviewSessions[payload.ots_id]) {
                    if (chatPreviewSessions[payload.ots_id].subscriber) chatPreviewSessions[payload.ots_id].subscriber.destroy();
                    if (chatPreviewSessions[payload.ots_id].session) chatPreviewSessions[payload.ots_id].session.disconnect();
                    delete chatPreviewSessions[payload.ots_id];
                }
                var currentSubtype = iconVideoCall;

                if (payload.subtype == 'audio') {
                    currentSubtype = iconAudioCall;
                }
                if (modalLoadingCall) {
                    console.log('101010')
                    modalLoadingCall.modal('hide');
                    modalLoadingCall = null;
                }
                if (callWindowModal) {
                    console.log('133333')
                    callWindowModal.modal('hide');
                    callWindowModal = null;
                }

                mediaMessage.html('<i class="fa fa-' +
                    currentSubtype + ' call-not-answered"></i> ' +
                    trCallNoAnswer + '</div>');

                removeCallPreviewClasses(mediaMessage);

                if (payload.from == currentUserId) {
                    // i am not the caller
                    // mediaMessage.html(trCallDropped);
                } else {
                    // mediaMessage.html(trCallDeclined);

                    playCallingBusySound();
                }

                currentCalls = unsetArrayElementByValue(currentCalls, roomId);
                switchButtonsToOnline();

                if (typeof callingModals[payload.room_id] != 'undefined' &&
                    callingModals[payload.room_id] != null) {
                    closeCallingModal(payload.room_id);
                }
            } else if (payload.subtype == 'hang-up') {
                console.log('i am in hagup')
                console.log('chatPreviewSessions[payload.ots_id]', chatPreviewSessions[payload.ots_id])

                if (chatPreviewSessions[payload.ots_id]) {
                    if (chatPreviewSessions[payload.ots_id].subscriber) chatPreviewSessions[payload.ots_id].subscriber.destroy();
                    if (chatPreviewSessions[payload.ots_id].session) chatPreviewSessions[payload.ots_id].session.disconnect();
                    delete chatPreviewSessions[payload.ots_id];
                }

                if (modalLoadingCall) {
                    console.log('144444')
                    modalLoadingCall.modal('hide');
                    modalLoadingCall = null;
                }

                // Close the call window modal for BOTH initiator and receiver
                if (callWindowModal) {
                    console.log('155555')
                    callWindowModal.modal('hide');
                    callWindowModal = null;
                }

                // Update the media message for all parties using the correct ots-id selector
                var currentSubtype = iconVideoCall;
                if (payload.method == 'audio') {
                    currentSubtype = iconAudioCall;
                }
                var hangUpMediaMessage = $(
                    '#room-' + roomId + ' .media.call[ots-id="' + payload.ots_id + '"] .panel-body');
                hangUpMediaMessage.html('<i class="fa fa-' +
                    currentSubtype + ' call-not-answered"></i> ' +
                    trCallNoAnswer + '</div>');
                removeCallPreviewClasses(hangUpMediaMessage);

                // Only the initiator (who closed the window) should call declineCallUrl
                if (payload.from == currentUserId) {
                    $.ajax({
                        url: declineCallUrl,
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            ots_id: payload.ots_id,
                            rel_id: payload.room_id,
                        }
                    }).fail(function (error) {
                        // Silently fail
                    });
                }

                currentCalls = unsetArrayElementByValue(currentCalls, roomId);
                switchButtonsToOnline();

                if (typeof callingModals[payload.room_id] != 'undefined' &&
                    callingModals[payload.room_id] != null) {
                    closeCallingModal(payload.room_id);
                }
            }
        } else if (payload.type == 'limitation_reached') {
            if (chatPreviewSessions[payload.ots_id]) {
                if (chatPreviewSessions[payload.ots_id].subscriber) chatPreviewSessions[payload.ots_id].subscriber.destroy();
                if (chatPreviewSessions[payload.ots_id].session) chatPreviewSessions[payload.ots_id].session.disconnect();
                delete chatPreviewSessions[payload.ots_id];
            }
            if (payload.from == currentUserId) {
                if (modalLoadingCall) {
                    console.log('166666')
                    modalLoadingCall.modal('hide');
                    modalLoadingCall = null;
                }
                if (callWindowModal) {
                    console.log('177777')
                    callWindowModal.modal('hide');
                    callWindowModal = null;
                }
                console.log('188888')
                closeAllCallingModals(); // Close any calling modals first
                bootbox.hideAll();
                showPaymentModal(payload.subtype);

                return;
            }
        } else if (payload.type == 'call-close') {
            roomId = payload.room_id;
            var mediaMessage = $(
                '#room-' + roomId + ' .media.call:last .panel-body');

            if (payload.from == currentUserId) {
                // i am not the caller
                mediaMessage.html(trCallClosed);
            } else {
                mediaMessage.html(trMissedCall);
            }

            removeCallPreviewClasses(mediaMessage);

            if (chatPreviewSessions[payload.ots_id]) {
                if (chatPreviewSessions[payload.ots_id].subscriber) chatPreviewSessions[payload.ots_id].subscriber.destroy();
                if (chatPreviewSessions[payload.ots_id].session) chatPreviewSessions[payload.ots_id].session.disconnect();
                delete chatPreviewSessions[payload.ots_id];
            }


            if (modalLoadingCall) {
                console.log('199999')
                modalLoadingCall.modal('hide');
                modalLoadingCall = null;
            }
            if (callWindowModal) {
                console.log('200000')
                callWindowModal.modal('hide');
                callWindowModal = null;
            }

            switchButtonsToOnline();

            if (typeof callingModals[payload.room_id] != 'undefined' &&
                callingModals[payload.room_id] != null) {
                closeCallingModal(payload.room_id);
            }
        }
    } catch (e) {
        safeNotify(e);
    }
}

function applyPlay(e, obj) {
    try {
        e.stopPropagation();
        e.preventDefault();

        var _link = obj.data('href');
        var messageId = obj.data('message-id');

        $.ajax({
            url: _link,
            type: 'POST'
        }).done(function (data) {
            if (data.error_flag == false) {
                if (data.permission == 'granted') {
                    $('.number-active-credits').html(data.user_data.credits);
                    $('.amount-credits').html(data.user_data.credits);
                    SocApp.updateCreditButtons(data.user_data);

                    if (data.displayRemainingTime && data.remaining_audio_listening_time != undefined) {

                        if (obj.parents('.received-audio-' + messageId).find('.timer-div').length <= 0) {
                            obj.parents('.received-audio-' + messageId).append('' +
                                '<div class="timer-div" style="display: flex; justify-content: end;">' + listenFreeFor + ' &nbsp;' +
                                '<div class="new-timer new-timer-' + messageId + '" data-seconds-left="' + data.remaining_audio_listening_time + '"></div>&nbsp;' +
                                minutes + '<i class="fad fa-clock"></i>'
                            );
                            $('.new-timer-' + messageId).startTimer({
                                onComplete: function (element) {
                                    element.parents('.received-audio-' + messageId).find('.timer-div').remove()
                                    element.parents('.received-audio-' + messageId).find('.new-timer-' + messageId).remove()
                                }
                            });
                        }
                    }
                } else {
                    if (data.show_modal) {
                        bootbox.hideAll();
                        showPaymentModal(null, null, null, null, data.message);
                    } else {
                        bootbox.dialog({
                            message: data.message,
                            title: trChat,
                            buttons: {
                                main: {
                                    label: "OK"
                                }
                            }
                        });
                    }
                }
            } else {
                bootbox.dialog({
                    message: data.message,
                    title: trChat,
                    buttons: {
                        main: {
                            label: "OK"
                        }
                    }
                });
            }
        }).fail(function (jqXHR) {
            if (jqXHR.status == 204) {
                bootbox.dialog({
                    message: jqXHR.responseText,
                    title: trChat,
                    buttons: {
                        main: {
                            label: "OK"
                        }
                    }
                });
            }
        });
    } catch (sException) {
        safeNotify(sException);

    }
}

function applyChatVideoPlay(e, obj) {
    try {
        e.stopPropagation();
        e.preventDefault();

        var _link = obj.data('href');
        var messageId = obj.data('message-id');

        $.ajax({
            url: _link,
            type: 'POST'
        }).done(function (data) {
            if (data.error_flag == false) {

                if (data.permission == 'granted') {
                    $('.number-active-credits').html(data.user_data.credits);
                    $('.amount-credits').html(data.user_data.credits);
                    SocApp.updateCreditButtons(data.user_data);

                    if (data.displayRemainingTime && data.remaining_chat_video_play_time != undefined) {
                        if (obj.parents('.received-video-' + messageId).find('.video-timer-div').length <= 0) {
                            obj.parents('.received-video-' + messageId).append('' +
                                '<div class="video-timer-div" style="display: flex; justify-content: end;">' + playFreeFor + ' &nbsp;' +
                                '<div class="new-timer new-video-timer-' + messageId + '" data-seconds-left="' + data.remaining_chat_video_play_time + '"></div>&nbsp;' +
                                minutes + '<i class="fad fa-clock"></i>'
                            );
                            $('.new-video-timer-' + messageId).startTimer({
                                onComplete: function (element) {
                                    element.parents('.received-video-' + messageId).find('.video-timer-div').remove()
                                    element.parents('.received-video-' + messageId).find('.new-video-timer-' + messageId).remove()
                                }
                            });
                        }
                    }

                    if (data.updated_user_data && data.updated_user_data.permission == 'denied') {
                        $('.received-video').each(function () {
                            let messageId = $(this).data('message-id');
                            if ($(this).find('.video-timer-div').length <= 0) {
                                $(this).find('.active-video-message-' + messageId).attr('data-href', data.inactive_chat_video_url)
                                $(this).find('.active-video-message-' + messageId).after('<button class="btn btn-sm btn-success blurred-video-message-play" data-message-id="' + data.message_id + '"><i class="fa fa-solid fa-play" aria-hidden="true"></i>' + clickToPlay + '</button>')
                                $(this).find('.active-video-message-' + messageId).removeClass('active-video-message')
                                $(this).find('.active-video-message-' + messageId).addClass('blurred-video')
                                $(this).find('.video-message-source').attr('src', data.inactive_chat_video_url)
                                $(this).attr('data-href', data.inactive_chat_video_url);
                            }

                        })
                    }
                } else {
                    if (data.show_modal) {
                        bootbox.hideAll();
                        showPaymentModal(null, null, null, null, data.message);
                    } else {
                        bootbox.dialog({
                            message: data.message,
                            title: trChat,
                            buttons: {
                                main: {
                                    label: "OK"
                                }
                            }
                        });
                    }
                }
            } else {
                bootbox.dialog({
                    message: data.message,
                    title: trChat,
                    buttons: {
                        main: {
                            label: "OK"
                        }
                    }
                });
            }
        }).fail(function (jqXHR) {
            if (jqXHR.status == 204) {
                bootbox.dialog({
                    message: jqXHR.responseText,
                    title: trChat,
                    buttons: {
                        main: {
                            label: "OK"
                        }
                    }
                });
            }
        });
    } catch (sException) {
        safeNotify(sException);

    }
}

function applyPause(e, obj) {
    try {
        e.stopPropagation();
        e.preventDefault();

        let messageId = obj.data('message-id');

        $.ajax({
            url: pauseMessageAudioAction,
            data: { 'message_id': messageId }
        }).done(function (data) {
            if (data.error_flag == false) {
                if (data.permission == 'granted') {
                    $('.number-active-credits').html(data.user_data.credits);
                    $('.amount-credits').html(data.user_data.credits);
                } else {
                    $('.received-audio').each(function () {
                        $(this).find('.active-audio-message').attr('data-href', data.inactive_audio_url)
                        $(this).find('.active-audio-message').before('<a href="javascript:;"><i class="fa fa-solid fa-play"></i> <span class="inactive-click-listen">' + clickToListen + '</span> </a>')
                        $(this).find('.audio-message-source').attr('src', data.inactive_audio_url)
                        $(this).addClass('inactive-audio');
                        $(this).attr('data-href', data.inactive_audio_url);
                    })
                }
            } else {
                bootbox.dialog({
                    message: data.message,
                    title: trChat,
                    buttons: {
                        main: {
                            label: "OK"
                        }
                    }
                });
            }
        }).fail(function (jqXHR) {
            if (jqXHR.status == 204) {
                bootbox.dialog({
                    message: jqXHR.responseText,
                    title: trChat,
                    buttons: {
                        main: {
                            label: "OK"
                        }
                    }
                });
            }
        });
    } catch (sException) {
        safeNotify(sException);

    }
}

var appendChatMessage = function (payload) {


    console.log("in append message")
    try {
        var roomId = payload.room_id;
        var messageId = payload.message_id;

        var isActive = $('#room-' + roomId).hasClass('active');

        if (isOnChatPage() == false && isInCallWindow == false) {
            if (payload.from_typing == false) {
                appendAlert(payload);

                $('.unread-messages-alert').removeClass('hidden')
                var unreadMsg = $('.unread-messages-alert').html();
                $('.unread-messages-alert').html(parseInt(unreadMsg) + 1);
                //insertAlert(payload);

                if (payload.type == 'call') {
                    displayCallingModal(payload);
                }
            }

            return;
        } else if (isActive) {
            if (payload.from !== currentUserId && payload.from_typing == false) {
                $.ajax({
                    url: messageMarkAsRealReadUrl,
                    data: { 'messageId': messageId, 'fromUser': payload.from }
                }).done(function (data) {
                    setupSorting();
                    var count = parseInt($('#number-of-unread-alerts').html()) - 1;

                    if (count > 0) {
                        $('#number-of-unread-alerts').html(count)
                    } else {
                        $('#number-of-unread-alerts').addClass('hidden');
                    }
                });
            }

        }

        var user = payload.user;
        var fullName = payload.full_name;
        var userAvatar = payload.user_avatar;
        var from = payload.from;
        var otsId = payload.ots_id;

        clearNoRecentMessages(roomId);

        var currentTime = new Date();
        var currentTimeIso = currentTime.toISOString();
        var _message = '';
        var subtype = '';

        if (payload.type == 'gift') {
            _message = _tplGiftMessage;
        } else if (payload.type == 'message' || payload.type == 'photo' || payload.type == 'audio' || payload.type == 'chat-video') {
            _message = _tplMessage;
        } else if (payload.type == 'call') {
            _message = _tplCall;
            var element = $(_tplCall);
            var callItem = element.closest('.call-preview-item');

            if (callItem.length) {
                console.log('i am innnnnnn-----------------------------')
                // _message = _message.replace('{%direction%}', '');
                _message = _message.replace('{%from%}', '');
                _message = _message.replace('{%user_avatar%}', '');
                _message = _message.replace('{%time%}', '');

            }
            console.log('_message after unwrap', _message)


            if (roomId == currentRelId) {
                switchButtonsToCall();
            }

            currentCalls.push(roomId);

            subtype = payload.subtype;

            _message = _message.replace('{%from%}', fullName).replace('{%user_avatar%}', payload.user_avatar_sidebar);
        }

        console.log(payload);
        var msg = '';
        if (payload.type == 'photo') {
            msg = _tplPhotoChat;
            msg = msg.replace('{%photo_url%}', payload.view_photo_url);
            msg = msg.replace('{%encode%}', payload.blurred_image_encode);
        } else if (payload.type == 'audio') {
            if (payload.from !== currentUserId) {
                msg = payload.allowed_listen_audio_to_user ? _tplActiveAudioChat : _tplInactiveAudioChat;
                msg = msg.replaceAll('{%class%}', 'received-audio received-audio-' + payload.message_id);
                msg = msg.replaceAll('{%oncontextmenu%}', 'oncontextmenu="return false;"');
                msg = msg.replaceAll('{%active_class%}', 'active-audio-message active-audio-message-' + payload.message_id);
            } else {
                msg = _tplActiveAudioChat;
                msg = msg.replaceAll('{%class%}', '');
                msg = msg.replaceAll('{%oncontextmenu%}', '');
                msg = msg.replaceAll('{%active_class%}', '');
            }

            msg = msg.replaceAll('{%message_id%}', payload.message_id);
            msg = msg.replaceAll('{%inactive_audio_url%}', payload.inactive_audio_url);
            msg = msg.replaceAll('{%active_audio_url%}', payload.play_audio_url);
        } else if (payload.type == 'chat-video') {
            if (payload.from !== currentUserId) {
                msg = payload.allowed_play_video_to_user ? _tplActiveVideoChat : _tplInactiveVideoChat;
                msg = msg.replaceAll('{%class%}', 'received-video received-video-' + payload.message_id);
                msg = msg.replaceAll('{%oncontextmenu%}', 'oncontextmenu="return false;"');
                msg = msg.replaceAll('{%active_class%}', 'active-video-message active-video-message-' + payload.message_id);
            } else {
                msg = _tplActiveVideoChat;
                msg = msg.replaceAll('{%class%}', '');
                msg = msg.replaceAll('{%oncontextmenu%}', '');
                msg = msg.replaceAll('{%active_class%}', '');
            }

            msg = msg.replaceAll('{%message_id%}', payload.message_id);
            msg = msg.replaceAll('{%inactive_video_url%}', payload.inactive_video_url);

            msg = msg.replaceAll('{%active_video_url%}', payload.play_video_url);
            msg = msg.replace('{%get_video_message_url%}', payload.get_video_url);
        } else {
            msg = payload.msg;
            msg = msg.replace(/(?:\r\n|\r|\n)/g, '<br />');
        }

        _message = _message.replaceAll('{%partner_name%}', fullName).replaceAll('{%message%}', msg).replaceAll('{%time%}', currentTimeIso).replaceAll('{%message_id%}', messageId).replaceAll('{%ots_id%}', otsId);
        var direction = '';
        if (payload.from == currentUserId) {
            direction = 'right';
        } else {
            direction = 'left';
        }

        _message = _message.replace('{%direction%}', direction);
        var _media = _tplMedia.replace('{%direction%}', direction).replace('{%user_avatar%}', userAvatar);

        if (direction == 'left') {
            _message = _message.replace('{%media_right%}', '').replace('{%media_left%}', _media);

            if (payload.type == 'call') {
                _message = _message.replace('{%call_direction%}', _tplCallLeft);
                _message = _message.replace('{%call_direction2%}',
                    _tplCallLeft2);
                _message = _message.replace('{%rel_id%}', roomId).replace('{%rel_id%}', roomId);
                _message = _message.replace('{%type%}', payload.subtype);
                _message = _message.replace('{%ots_id%}', payload.ots_id).replace('{%ots_id%}', payload.ots_id);
                _message = _message.replace('{%call_type%}', subtype + ' ' +
                    (subtype == 'audio' ? '<i class="fa fa-' + iconAudioCall +
                        '"></i> ' : '<i class="fa fa-' + iconVideoCall +
                    '"></i> '));
            }

        } else {
            _message = _message.replace('{%media_left%}', '').replace('{%media_right%}', _media);

            if (payload.type == 'call') {
                _message = _message.replace('{%call_direction%}',
                    _tplCallRight);
                _message = _message.replace('{%call_direction2%}',
                    _tplCallRight2);
                _message = _message.replace('{%rel_id%}', roomId).replace('{%rel_id%}', roomId);
                _message = _message.replace('{%type%}', payload.subtype);
                _message = _message.replace('{%ots_id%}', payload.ots_id).replace('{%ots_id%}', payload.ots_id);
                _message = _message.replace('{%call_type%}', subtype + ' ' +
                    (subtype == 'audio' ? '<i class="fa fa-' + iconAudioCall +
                        '"></i> ' : '<i class="fa fa-' + iconVideoCall +
                    '"></i> '));
            }
        }
        console.log(_message)
        var element = $(_message);
        var callItem = element.closest('.call-preview-item');
        if (callItem.length > 0) {

            var nextDiv = callItem.children('div').first();
            nextDiv.addClass('full-screen-preview');
            // nextDiv.find('.panel-body.call-preview-content').css('background', 'none !important');
            nextDiv.find('.panel-body.call-preview-content').each(function () {
                this.style.setProperty('background', 'none', 'important');
            });
            console.log('oooooooooooooooooooooo', nextDiv.prop('outerHTML'))
            _message = callItem.prop('outerHTML')
        }

        var message_setting = '';
        var message_countdown_time = '';
        var isLoading = true
        let current_user_id = $('#current-user-id').val();

        console.log('API CALL', '1');

        $.ajax({
            type: 'POST',
            url: checkMessageUrl,
            data: { user_id: current_user_id },
            async: false
        })
            .done(function (response) {
                message_setting = response.messageSettings;
                message_countdown_time = response.countdownTime;
                console.log('Message checked response:', response.messageSettings);
                console.log('Message checked response:', response.countdownTime);
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                console.log('API call failed:', textStatus, errorThrown);
            });

        console.log('fresh setting:', message_setting);
        console.log('fresh countdown:', message_countdown_time);

        if (direction == 'left') {
            if (message_setting && payload.from_typing == false) {
                let copyTplBlurredMessage = _tplBlurredMessage;
                copyTplBlurredMessage = copyTplBlurredMessage.replaceAll('{%message_id%}', payload.message_id);
                copyTplBlurredMessage = copyTplBlurredMessage.replace('{%time%}', currentTimeIso);
                copyTplBlurredMessage = copyTplBlurredMessage.replace('{%countdownTime%}', message_countdown_time);
                copyTplBlurredMessage = copyTplBlurredMessage.replace('{%user_avatar%}', payload.user_avatar_sidebar);
                $('#room-' + roomId).append(copyTplBlurredMessage);
                $('#chat-last-message').hide();
                $('.last-message').hide();
                // Set the countdown time in hours from the backend
                let countdownTimeInHHMMSS = message_countdown_time
                console.log('countdownTimeInHHMMSS', countdownTimeInHHMMSS)

                // Extract hours, minutes and days from the string
                let [days, hours, minutes] = countdownTimeInHHMMSS.split(":").map(Number);

                // Convert the time to total seconds
                let countdownTimeInSeconds = (days * 86400) + (hours * 3600) + (minutes * 60);

                let cardReadTimestamp = payload.count_down_start_time;
                let mytime = moment();
                console.log('cardReadTimestamp========', cardReadTimestamp);
                console.log('mytime===========', mytime);

                // Get the user's timezone
                let userTimezone = moment.tz.guess();
                console.log('userTimezone', userTimezone);

                // Convert backend timestamp to user's timezone
                let cardReadTime = moment.tz(cardReadTimestamp.date, cardReadTimestamp.timezone).unix();
                console.log('cardReadTime (converted to user timezone)', cardReadTime);

                // Get the current time in the user's timezone
                let currentTimeNow = moment().tz(userTimezone).unix();
                console.log('currentTimeNow', currentTimeNow);

                // Calculate the elapsed time in seconds
                let elapsedTime = currentTimeNow - cardReadTime;
                console.log('elapsedTime', elapsedTime);

                // Subtract the elapsed time from the countdown time
                countdownTimeInSeconds -= elapsedTime;
                console.log('countdownTimeInSeconds', countdownTimeInSeconds);

                // If the countdown time has passed, clear the interval
                if (countdownTimeInSeconds <= 0) {
                    //$('#messageCard-' + messageId).remove();
                    //$('#message_container_' + messageId).remove();
                    //updateMessageStatusToDeleted();
                    // Make AJAX call to mark the message as deleted
                }

                const countdownElement = $('#countdown-' + messageId);
                console.log('countdownElement', countdownElement)
                let countdownInterval = setInterval(function () {
                    if (countdownTimeInSeconds <= 0) {
                        clearInterval(countdownInterval);
                        // Remove the message card once the countdown ends
                        $('#messageCard-' + messageId).remove();
                        // Make AJAX call to mark the message as deleted
                        updateMessageStatusToDeleted();
                        $('#message_container_' + messageId).remove();
                    } else {
                        // Calculate remaining days, hours, minutes, and seconds
                        let remainingDays = Math.floor(countdownTimeInSeconds / 86400); // Remaining days
                        let remainingHours = Math.floor((countdownTimeInSeconds % 86400) / 3600); // Remaining hours
                        let remainingMinutes = Math.floor((countdownTimeInSeconds % 3600) / 60); // Remaining minutes
                        let remainingSeconds = countdownTimeInSeconds % 60; // Remaining seconds

                        // Prepare the countdown message
                        let countdownMessage = '';

                        // If there are days remaining, show the message as "X days Y hours Z minutes left"
                        if (remainingDays > 0) {
                            countdownMessage = `${remainingDays} day${remainingDays > 1 ? 's' : ''} ${remainingHours < 10 ? '0' : ''}${remainingHours} hrs ${remainingMinutes < 10 ? '0' : ''}${remainingMinutes} mins ${remainingSeconds < 10 ? '0' : ''}${remainingSeconds} secs`;
                        } else {
                            // If no days left, only show hours, minutes, and seconds
                            countdownMessage = `${remainingHours < 10 ? '0' : ''}${remainingHours} hrs ${remainingMinutes < 10 ? '0' : ''}${remainingMinutes} mins ${remainingSeconds < 10 ? '0' : ''}${remainingSeconds} secs`;
                        }

                        // Update the countdown timer on the page
                        countdownElement.html(`<i class="fa fa-clock" style="color: #21c55d"></i> ${countdownMessage}`);

                        // Decrease the countdown time by 1 second
                        countdownTimeInSeconds--;
                    }
                }, 1000);

                // Handle clicks on the message card or the button to open payment method
                function openPaymentMethod() {
                    closeAllCallingModals(); // Close any calling modals first
                    bootbox.hideAll();
                    showPaymentModal();
                    return false;
                }

                // Click on the message card or button will open the payment method
                $('#messageCard-' + messageId).on('click', openPaymentMethod);
                $('.btn-open-payment').on('click', openPaymentMethod);

                // Function to update message status to deleted through AJAX
                function updateMessageStatusToDeleted() {
                    $.ajax({
                        type: 'POST',
                        url: deleteMessageUrl,
                        async: true,
                        data: { messageId: messageId }
                    }).done(function (response) {
                        console.log('Message marked as deleted:', response);
                    });
                }
            } else {
                $('#room-' + roomId).append(_message);
            }
        } else {
            $('#room-' + roomId).append(_message);
        }

        if (payload.type == 'audio') {
            $.ajax({
                type: 'POST',
                url: playAudioMessageUrl,
                async: true,
                data: { messageId: payload.message_id }
            }).done(function (response) {
                if (response.error_flag == false) {
                    $('.audio-' + response.messageId).attr('src', response.message);
                }
            });

            $('.active-audio-message-' + payload.message_id).on('play', function (e) {
                applyPlay(e, $(this));
            })

            $('.active-audio-message-' + payload.message_id).on('pause', function (e) {
                applyPause(e, $(this));
            })

            $('.active-audio-message-' + payload.message_id).bind('contextmenu', function (e) {
                e.preventDefault();
            });
        }

        if (payload.type == 'chat-video') {
            $.ajax({
                type: 'POST',
                url: playVideoMessageUrl,
                async: true,
                data: { messageId: payload.message_id }
            }).done(function (response) {
                if (response.error_flag == false) {
                    $('.video-' + response.messageId).attr('src', response.message);
                }
            });

            $('.active-video-message-' + payload.message_id).on('play', function (e) {
                applyChatVideoPlay(e, $(this));
            })

            $('.active-video-message-' + payload.message_id).bind('contextmenu', function (e) {
                e.preventDefault();
            });
        }

        var chatCanvas = document.getElementById('room-' + roomId);

        chatCanvas.scrollTop = chatCanvas.scrollHeight;

        setupSorting();

        $('abbr.timeago:empty').timeago();

        if (currentUserId != from) {
            var msgDisplay = '';
            if (msg.length > 30) {
                msgDisplay = msg.substr(0, 20) + '...';
            } else {
                msgDisplay = msg;
            }

            if (payload.type !== 'gift') {
                $('.contact-list-item[relationship-id="' + roomId + '"] .last-message').html(msgDisplay);
            }

            if (payload.type == 'audio') {
                $('.contact-list-item[relationship-id="' + roomId + '"] .last-message').html('Sent Audio Message');
            }
            if (payload.type == 'chat-video') {
                $('.contact-list-item[relationship-id="' + roomId + '"] .last-message').html('Sent Video Message');
            }
            if (roomId == currentRelId) {
                $('#chat-last-message').html(msgDisplay);
            }

            if (payload.from_typing == false) {
                if (payload.type == 'message' || payload.type == 'photo' || payload.type == 'audio' || payload.type == 'chat-video' || payload.type == 'gift') {
                    playMessageReceivedSound();
                } else {
                    playCallingSound();
                }
            }
        } else {
            // setTimeout(function() {
            //     // if (typeof relId != 'undefined' && typeof openTokData[relId] != 'undefined' && openTokData[relId]['currentState'] == 'calling') {
            //         chatCallParams = {type: 'call-close', subtype: payload.subtype, msg: '', for_message_id: messageId};
            //
            //         addPayload(relId, chatCallParams);
            //
            //         playCallingBusySound();
            //     // }
            // }, callClosedDelay);
        }
        if ((roomId != currentRelId && payload.from_typing == false) ||
            (isInCallWindow && isChatOpened == false && payload.from_typing == false)) {
            var noMessagesArea = $(
                '.contact-list-item[relationship-id="' + roomId +
                '"] .contact-no-messages');
            noMessagesArea.html(parseInt(noMessagesArea.html()) + 1);
            appendAlert(payload);
            noMessagesArea.removeClass('hidden');

            $('.unread-messages-alert').removeClass('hidden')
            var unreadMsg = $('.unread-messages-alert').html();
            $('.unread-messages-alert').html(parseInt(unreadMsg) + 1);

            updateNumberOfUnreadMessagesByUser();
            updateTotalUnreadMessages();
            setupSorting();
        }

        scrollToBottomOfRoomChat(roomId);
        if (payload.type == 'call' && payload.from != currentUserId) {
            //  displayCallingModal(payload);
            initCallPreviewInChat(payload);
        }

        $('#room-' + roomId).css('left', '0');
        if (currentRelId != roomId) {
            $('#room-' + roomId).addClass('hidden');
            $('#room-' + roomId).removeClass('active');
        }
    } catch (sException) {
        console.log("exception")
        safeNotify(sException);

    }
};


var insertAlert = function (payload) {
    $.ajax({
        url: alertInsert,
        type: 'POST',
        dataType: 'JSON',
        data: { payload: payload }
    }).done(function (data) {
    });
};

var markRelationshipAsOnlineOrOffline = function (payload) {
    try {
        $('.grid-filter-options a.active').trigger('click');
        var roomId = 0;
        var status = null;

        if (payload.from != currentUserId) {
            if (payload.status == 2) {
                status = 'online';
            } else if (payload.status == 1) {
                status = 'away';
            } else {
                status = 'offline';
            }

            $('.contact-list-item[relationship-id="' + payload.room_id + '"]').closest('.grid-contact-item').removeClass('offline').addClass('online').attr('data-status', payload.status).attr('data-groups', '["' + status + '"]').data('groups', [status]).find('span.status').removeClass('status-online').removeClass('status-offline').removeClass('status-away').addClass('status-' + status);

            if (currentRelId == payload.room_id) {
                $('#chat-status').removeClass('status-online').removeClass('status-offline').removeClass('status-away');
                $('#chat-status').addClass('status-' + status).data('original-title', status.capitalizeFirstLetter()).attr('data-original-title', status.capitalizeFirstLetter());

                roomId = payload.room_id;
                if (payload.status == 2) {
                    switchButtonsToOnline();
                } else {
                    switchButtonsToOffline();
                }
            }

            if (status == 'offline') {

            }

            setupSorting();
        }
    } catch (e) {
        safeNotify(e);
    }
};

function bindUi() {
    try {
        $('body').on('click', '#send-chat', function (e) {
            e.preventDefault();
            publishChat('normal');
        });

        $('body').on('keypress', '#chat-input', function (e) {

            var code = (e.keyCode ? e.keyCode : e.which);

            if (e.ctrlKey == false && (code == 13 || code == 10)) { //Enter keycode
                e.stopPropagation();
                e.preventDefault();

                if (sendingMessage == false) {
                    publishChat('normal');
                }

            } else if (e.ctrlKey == true && (code == 13 || code == 10)) {
                e.stopPropagation();
                e.preventDefault();


                if (sendingMessage == false) {
                    $('#chat-input').val($('#chat-input').val() + "\r");
                }
            }
        });

        $('body').on('keyup', '#chat-input', function (e) {
            var code = (e.keyCode ? e.keyCode : e.which);

            var val = $('#chat-input').val();
            if (code == 8 || code == 46) {
                if (val == '') {
                    /**
                     * send is typing terminated
                     */
                    Chat.triggerTypingStop(currentRelId, currentUserId);
                    startedTyping[currentRelId] = 0;
                }
            } else {
                /**
                 * send is typing started
                 */

                if (typeof startedTyping[currentRelId] == 'undefined' ||
                    startedTyping[currentRelId] == 0) {
                    Chat.triggerTypingStart(currentRelId, currentUserId);
                    startedTyping[currentRelId] = 1;
                }
            }

        });

        $('#chat-input').attr('disabled', false);
    } catch (e) {
        safeNotify(e);
    }
}

// attempt to clean up on disconnect.
function unbindUi() {
    try {
        $("#join-chat").unbind();
        $("#chatroom").unbind();

        $("#send-chat").unbind();
        $("#chat-input").unbind();
        $('#chat-input').attr('disabled', true);
        // $('.btn-calls').addClass('hidden');

        $('.contact-list-item .status').removeClass('status-online').addClass('status-offline');
        $('.contact-list-item[relationship-id="' + currentRelId + '"]').trigger('click');
    } catch (e) {
        safeNotify(e);
    }
}

var checkMessageVisibility = function (_area) {
    try {
        _area.find('.media:not(".media-history.scroll-event-added")').each(function () {
            if ($(this).is_on_screen()) {
                $(this).addClass('visible');
            } else {
                $(this).removeClass('visible');
            }
        });
    } catch (e) {
        safeNotify(e);
    }
};

var updateNumberOfUnreadMessagesByUser = function () {
    try {
        var total = 0;

        var noMessagesAreas = $('.contact-list-item .contact-no-messages');
        $.each(noMessagesAreas, function (pos, elem) {
            var _elNoNumberOfUnreadMessages = parseInt($(elem).html());
            total += _elNoNumberOfUnreadMessages;
        });

        $('#current-number-of-unread-messages').html(total).data('total-unread-messages', total);
        if (total == 0) {
            $('#current-number-of-unread-messages').addClass('hidden');
        }
        $('#current-number-of-unread-messages').data('total-unread-messages', total);
    } catch (e) {
        safeNotify(e);
    }
};

var updateTotalUnreadMessages = function () {
    try {
        var total = 0;

        var noMessagesAreas = $('.contact-list-item .contact-no-messages');
        $.each(noMessagesAreas, function (pos, elem) {
            var _elNoNumberOfUnreadMessages = parseInt($(elem).html());
            total += _elNoNumberOfUnreadMessages;
        });

        $('.unread-messages-alert').html(total);
        if (total == 0) {
            $('.unread-messages-alert').addClass('hidden');
        }
    } catch (e) {
        safeNotify(e);
    }
};

var selectFirstContactOrChatFriend = function () {
    try {
        processingMessagesQueue = [];
        if (openRelId == '') {
            $(sortedContacts[0]).find('a.contact-list-item').trigger('click');
        } else {
            var elementToTrigger = $('.contact-list-item[relationship-id="' + openRelId + '"]');

            if ($(elementToTrigger).length == 0) {
                $(sortedContacts[0]).find('a.contact-list-item').trigger('click');
            } else {
                $(elementToTrigger).trigger('click');
            }
        }
    } catch (sExeption) {
        safeNotify('Error on ajax_chat :: ' + openRelId);
        safeNotify(sException);
    }
};

var switchButtonsToCall = function () {
    try {
        // $('.btn-calls').addClass('hidden');
        // $('.btn-calls.cancel-call').removeClass('hidden');
    } catch (sException) {
        safeNotify(sException);

    }
};

var switchButtonsToOnline = function () {
    try {
        // $('.btn-calls').addClass('hidden');
        $('.btn-calls:not(.cancel-call)').removeClass('hidden');
    } catch (sException) {
        safeNotify(sException);

    }
};

var switchButtonsToOffline = function () {
    // $('.btn-calls').addClass('hidden');
};

var clearNoRecentMessages = function (roomId) {
    try {
        $('#room-' + roomId).find('.no-recent-messaages').remove();
    } catch (sException) {
        safeNotify(sException);

    }
};

var hangUpCall = function (roomId, otsId) {
    try {
        $.ajax({
            url: hangUpCallUrl,
            type: 'POST',
            dataType: 'JSON',
            data: { ots_id: otsId, rel_id: roomId }
        }).done(function (data) {
            if (data.status == false) {
                bootbox.dialog({
                    message: trChatProblems,
                    title: trChat,
                    buttons: {
                        main: {
                            label: "OK"
                        }
                    }
                });
            } else {
            }
        }).fail(function (error) {
            safeNotify(error);
        });
    } catch (sExcpetion) {
        safeNotify(sExcpetion);
    }
};

var playCallingSound = function () {
    $.playSound('/bundles/socialfrontend/sound/calling');
};

var playMessageReceivedSound = function () {
    $.playSound('/bundles/socialfrontend/sound/message_received');
};

var playCallingBusySound = function () {
    $.playSound('/bundles/socialfrontend/sound/calling_busy');
};

var addPayload = function (roomId, data) {
    try {
        console.log("send message")
        var sendChatOldHtml = $('#send-chat').html();
        $('#send-chat').html('<i class="fas fa-spinner fa-pulse"></i>').attr('disabled', 'disabled');
        sendingMessage = true;
        $.ajax({
            url: addPayloadUrl + '?room_id=' + roomId,
            type: 'POST',
            dataType: 'JSON',
            data: data
        }).done(function (responseData) {
            sendingMessage = false;
            $('#send-chat').html(sendChatOldHtml).removeAttr('disabled');
            if (responseData.error_flag == false) {
                if (responseData.permission == 'denied') {
                    if (responseData.hasOwnProperty('silent') && responseData.silent == true) {
                        return;
                    }
                    if (responseData.muted == true) {
                        SocApp.displayFlashMessage({
                            "message": responseData.message,
                            type: 'danger',
                            icon: 'fas fa-times-circle'
                        });
                        return;
                    }
                    closeAllCallingModals(); // Close any calling modals first
                    bootbox.hideAll();
                    showPaymentModal(null, null, null, null, responseData.message);
                    if (isCreditMode == false || isCreditMode == 0) {
                        $('.upgrade-right-sidebar-widget').show().removeClass('hidden');
                        $('a.menu-upgrade-button').closest('li').show().removeClass('hidden');
                    }
                    return;
                } else {
                    if (responseData.user_data.credits !== undefined) {
                        $('.number-active-credits').html(responseData.user_data.credits);
                        $('.amount-credits').html(responseData.user_data.credits);
                        SocApp.updateCreditButtons(responseData);
                    }

                    if (responseData.open_profile_confirmation_modal) {
                        openProfileConfirmationModal();
                    }
                }
            }
            startedTyping[currentRelId] = 0;
        });
    } catch (e) {
        safeNotify(e);
    }
};

var callingModals = {};
var chatPreviewSessions = {};

var displayCallingModal = function (payload) {
    try {

        var subtype = payload.subtype;
        var fullName = payload.full_name;
        var otsId = payload.ots_id;
        var roomId = payload.room_id;

        var avatarUrl = "";
        if (payload.user_avatar_sidebar) {
            var match = payload.user_avatar_sidebar.match(/src="([^"]+)"/);
            if (match) avatarUrl = match[1];
        }

        var iconClass = (subtype == 'audio' ? iconAudioCall : iconVideoCall);

        var modalHtml =
            '<div class="incoming-call-container ' + (subtype == 'audio' ? 'audio-mode' : 'video-mode') + '">' +
            
            (subtype == 'video'
                ? '<div id="caller-preview-container" style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:1;">' +
                '<div id="caller-preview" style="position:absolute;top:0;left:0;width:100%;height:100%;background:#0b0f11;"></div>' +
                '<div style="position:absolute;bottom:30px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,0.6);padding:8px 15px;border-radius:20px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:8px;z-index:20;border:1px solid rgba(255,255,255,0.2);backdrop-filter:blur(4px);white-space:nowrap;">' +
                '<div style="width:8px;height:8px;background:#2ecc71;border-radius:50%;box-shadow:0 0 8px #2ecc71;"></div>' +
                '<i class="fa fa-video"></i> Caller Video Preview' +
                '</div>' +
                '</div>'
                : '') +

            '<div class="incoming-call-bg" style="background-image:url(' + avatarUrl + ');z-index:0;"></div>' +

            '<div class="incoming-call-content">' +
            '<div class="caller-avatar-wrapper">' +
            '<img src="' + avatarUrl + '" class="caller-avatar" alt="' + fullName + '">' +
            '<div class="call-type-indicator">' +
            '<i class="fa fa-' + iconClass + '"></i>' +
            '</div>' +
            '</div>' +
            '<h2 class="caller-name">' + fullName + '</h2>' +
            '<p class="call-status-label">' + trIsCallingYou2 + '</p>' +
            '<div class="call-actions-group">' +
            '<button class="call-btn decline decline-call" ots-id="' + otsId + '" rel-id="' + roomId + '" type="' + subtype + '" title="' + trDecline + '">' +
            '<i class="fa fa-times"></i>' +
            '</button>' +
            '<button class="call-btn ignore ignore-call" ots-id="' + otsId + '" rel-id="' + roomId + '" type="' + subtype + '" title="' + trIgnore + '">' +
            '<i class="fa fa-ellipsis-h"></i>' +
            '</button>' +
            '<button class="call-btn answer answer-call" ots-id="' + otsId + '" rel-id="' + roomId + '" type="' + subtype + '" title="' + trAnswer + '">' +
            '<i class="fa fa-phone"></i>' +
            '</button>' +
            '</div>' +
            '</div>' +
            '</div>';

        var callingModal = bootbox.dialog({
            message: modalHtml,
            buttons: {},
            className: "view-section-modal-answer-details"
        });

        callingModals[roomId] = callingModal;

        callingModal.data('ots-id', otsId);
        callingModal.data('rel-id', roomId);
        callingModal.data('call-type', subtype);

        /* ---------------------------------------
           CLEANUP WHEN MODAL CLOSES
        ----------------------------------------*/
        callingModal.on('hidden.bs.modal', function () {

            var otsId = $(this).data('ots-id');
            var relId = $(this).data('rel-id');
            var callType = $(this).data('call-type');

            var subscriber = $(this).data('preview-subscriber');
            if (subscriber) subscriber.destroy();

            var previewSession = $(this).data('preview-session');
            var isAnswering = $(this).data('is-answering');

            // If the user is answering, we MUST NOT disconnect the preview session here
            // because it would drop the connection for the initiator.
            //if (previewSession && !isAnswering) previewSession.disconnect();

            if (typeof callingModals[relId] != 'undefined' && callingModals[relId] != null) {

                $.ajax({
                    url: declineCallUrl,
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        ots_id: otsId,
                        rel_id: relId,
                        type: callType
                    }
                });

                delete callingModals[relId];
            }
        });

        /* ---------------------------------------
           VIDEO PREVIEW LOGIC
        ----------------------------------------*/

        var apiKey = (typeof openTokApiKey !== 'undefined')
            ? openTokApiKey
            : (window.openTokApiKey ? window.openTokApiKey : null);

        if (subtype == 'video'
            && typeof TB !== 'undefined'
            && apiKey
            && payload.session_id
            && payload.receiver_token) {

            setTimeout(function () {

                var previewTarget = callingModal.find('#caller-preview')[0];
                if (!previewTarget) return;

                $(previewTarget).html(
                    '<div class="waiting-text" style="color:rgba(255,255,255,0.5);font-weight:500;text-align:center;margin-top:45%;">' +
                    '<i class="fa fa-spinner fa-spin"></i> Waiting for caller...' +
                    '</div>'
                );

                var previewSession = TB.initSession(apiKey, payload.session_id);

                previewSession.on('streamCreated', function (event) {

                    // Subscribe only to sender stream
                    if (event.stream.connection.connectionId !== previewSession.connection.connectionId) {

                        $(previewTarget).find('.waiting-text').remove();

                        var subscriber = previewSession.subscribe(
                            event.stream,
                            previewTarget,
                            {
                                insertMode: 'append',
                                width: '100%',
                                height: '100%',
                                subscribeToAudio: false,
                                fitMode: 'cover'
                            }
                        );

                        subscriber.on('videoElementCreated', function (event) {

                            // Apply blur to actual video element
                            var blurVal = (typeof videoCallBlur !== 'undefined') ? videoCallBlur : 15;
                            console.log('blurVal', blurVal)
                            var brightnessVal = (typeof videoCallBrightness !== 'undefined') ? videoCallBrightness : 0.6;
                            console.log('brightnessVal', brightnessVal)
                            $(event.element).css({
                                width: '100%',
                                height: '100%',
                                objectFit: 'cover',
                                filter: 'blur(' + blurVal + 'px) brightness(' + brightnessVal + ')',
                                transform: 'scale(1.08)'
                            });

                        });

                        callingModal.data('preview-subscriber', subscriber);
                    }
                });

                previewSession.connect(payload.receiver_token, function (error) {
                    if (error) {
                        console.error('Preview session failed:', error.message);
                    }
                });

                callingModal.data('preview-session', previewSession);

            }, 100);
        }

    } catch (e) {
        console.error(e);
    }
};



var initCallPreviewInChat = function (payload) {
    try {
        var subtype = payload.subtype;
        var messageId = payload.message_id;
        var roomId = payload.room_id;

        var apiKey = (typeof openTokApiKey !== 'undefined')
            ? openTokApiKey
            : (window.openTokApiKey ? window.openTokApiKey : null);

        if (subtype == 'video'
            && typeof TB !== 'undefined'
            && apiKey
            && payload.session_id
            && payload.receiver_token) {

            setTimeout(function () {
                var previewTarget = $('#caller-preview-' + messageId)[0];

                if (!previewTarget) {
                    return;
                }
                $(previewTarget).show();

                var previewSession = TB.initSession(apiKey, payload.session_id);

                chatPreviewSessions[payload.ots_id] = {
                    session: previewSession,
                    subscriber: null
                };

                previewSession.on('sessionConnected', function (event) {
                });

                previewSession.on('streamCreated', function (event) {
                    // Subscribe only to remote stream
                    var isOurStream = (previewSession.connection && event.stream.connection.connectionId === previewSession.connection.connectionId);
                    if (!isOurStream) {
                        $(previewTarget).find('.waiting-text').remove();

                        // Force visibility
                        $(previewTarget).css({
                            'display': 'block',
                            'z-index': 0,
                            'background': 'transparent !important',
                            'position': 'absolute',
                            'top': 0,
                            'left': 0,
                            'width': '100%',
                            'height': '100%'
                        });
                        $(previewTarget).closest('.panel').css('background', 'transparent');

                        var subscriber = previewSession.subscribe(
                            event.stream,
                            previewTarget,
                            {
                                insertMode: 'append',
                                width: '100%',
                                height: '100%',
                                subscribeToAudio: false,
                                fitMode: 'cover'
                            },
                            function (err) {
                                if (err) {
                                    console.error('initCallPreviewInChat: Subscribe FAILED:', err.message);
                                } else {
                                    console.log('initCallPreviewInChat: Subscribe SUCCESSful');
                                }
                            }
                        );

                        subscriber.on('videoElementCreated', function (ev) {
                            // Remove default OT background
                            $(previewTarget).find('.OT_widget-container').css('background', 'none');

                            // Apply styles to the video element
                            var blurVal = (typeof videoCallBlur !== 'undefined') ? videoCallBlur : 15;
                            var brightnessVal = (typeof videoCallBrightness !== 'undefined') ? videoCallBrightness : 0.6;
                            $(ev.element).css({
                                'width': '100% !important',
                                'height': '100% !important',
                                'object-fit': 'cover',
                                'filter': 'blur(' + blurVal + 'px) brightness(' + brightnessVal + ')',
                                'transform': 'scale(1.1)', // Slightly more scale to cover edges better
                                'position': 'absolute',
                                'top': 0,
                                'left': 0,
                                'z-index': -2
                            });
                        });

                        subscriber.on('error', function (err) {
                            console.error('initCallPreviewInChat: Subscriber error:', err.message);
                        });

                        if (chatPreviewSessions[payload.ots_id]) {
                            chatPreviewSessions[payload.ots_id].subscriber = subscriber;
                        }
                    } else {
                        console.log('initCallPreviewInChat: This is our OWN stream (ignoring)');
                    }
                });

                previewSession.on('error', function (err) {
                    console.error('initCallPreviewInChat: Session error:', err.message);
                });

                previewSession.connect(payload.receiver_token, function (error) {
                    if (error) {
                        console.error('initCallPreviewInChat: Connect FAILED:', error.message);
                    } else {
                        console.log('initCallPreviewInChat: Connected successfully. Existing streams:', previewSession.streams.length);
                    }
                });

            }, 100);
        } else {
            console.log('initCallPreviewInChat: FAILED check', {
                subtype: subtype,
                hasTB: typeof TB !== 'undefined',
                hasApiKey: !!apiKey,
                hasSessionId: !!(payload && payload.session_id),
                hasToken: !!(payload && payload.receiver_token)
            });
        }

    } catch (e) {
        console.error(e);
    }
};


var closeCallingModal = function (roomId) {
    console.log('211111')
    try {
        callingModals[roomId].modal('hide');
        callingModals[roomId] = null;
    } catch (sException) {
        safeNotify(sException);

    }
};

var closeAllCallingModals = function () {
    console.log('2----------')
    try {
        for (var roomId in callingModals) {
            if (callingModals.hasOwnProperty(roomId) && callingModals[roomId] != null) {
                callingModals[roomId].modal('hide');
                callingModals[roomId] = null;
            }
        }
    } catch (sException) {
        safeNotify(sException);
    }
};

var scrollToBottomOfRoomChat = function (roomId) {
    try {
        if ($('#room-' + roomId).get(0) !== undefined) {
            var scrollTop = $('#room-' + roomId).css('left', '-9999').removeClass('hidden').get(0).scrollHeight;
            $('#room-' + roomId).scrollTop(scrollTop);
        }
    } catch (sException) {
        safeNotify(sException);

    }
};