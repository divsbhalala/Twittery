
$(document).ready(function () {
    var getUrl = window.location;
    //var baseUrl = getUrl.protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];  //for local
     var baseUrl = getUrl .protocol + "//" + getUrl.host;
    var htmldata = '';
    strtLoad();
    homwtweet();
    var lastusers = '';
    /*-------------------For getting my homes tweet-------------------*/
    function homwtweet() {
        $.ajax({
            url: "getData.php",
            data: {
                tweetType: 'home'
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function () {
                strtLoad();
            },
            success: function (data) {
                drawtweets(data);
            },
            error: function (ee, eee, eeee) {
                showError('Opps! error in tweet fatch', 'alert-danger');
                finishload();
            },
            complete: function () {
                finishload();
            }
        });
    }

    /*-------------------For getting user tweet-----------------------*/
    function usertweet(name) {
        lastusers = name;
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "getData.php",
            data: {
                tweetUserName: name,
                tweetType: 'followers'
            },
            beforeSend: function () {
                strtLoad();
            },
            success: function (data) {
                drawtweets(data);
            },
            error: function () {
                showError('Opps! error in tweet fatch', 'alert-danger');
                finishload();
            },
            complete: function (jqXHR, textStatus) {
                finishload();
            }
        });
    }

    /*-------------------Push tweet for slideshow---------------------*/
    function drawtweets(data) {
        strtLoad();
        htmldata = '';
        var arrTweets = [];
        var descriptions = [];
        var timeat = [];
        var retweetby = [];
        var tweetsfav = [];
        if (data.length > 0) {
            for (var i = 0; i < data.length; i++) {
                var username = '';
                var screenname = '';
                var retweeted = '';
                var profileimg = '';
                var rtcount = '';
                var fav = '';
                var desc = '';
                var uretweet = '';
                var media_url = '';


                var imgs = '';
                if (data[i].hasOwnProperty('retweeted_status')) {
                    username = data[i].retweeted_status.user.name;
                    screenname = data[i].retweeted_status.user.screen_name;
                    retweeted = '<span class="fa fa-retweet bg-custom"></span> ' + data[i].user.name + '   retweeted';
                    profileimg = data[i].retweeted_status.user.profile_image_url;
                    desc = data[i].retweeted_status.text;
                    fav = data[i].retweeted_status.favorite_count;
                    uretweet = 'now';

                }
                else {
                    username = data[i].user.name;
                    screenname = data[i].user.screen_name;
                    profileimg = data[i].user.profile_image_url;
                    desc = data[i].text;
                    fav = data[i].favorite_count;
                }
                if (data[i].hasOwnProperty('extended_entities')) {
                    imgs = data[i].extended_entities.media[0].media_url;
                    media_url = '<div class="col-md-12 pull-left">' +
                            '<img src="' + imgs + '" class="img-responsive img-rounded">' +
                            '</div>';
                }
                arrTweets.push({
                    href: imgs,
                    title: username + ' @' + screenname,
                    description: desc


                });
                rtcount = data[i].retweet_count;
                var str = '';
                str = '<label class="count-box now "><span class="fa fa-retweet text-muted ' + uretweet + '"></span>' + rtcount + '&nbsp;Retweet</label>' +
                        '<label class="count-box now"><span class="fa fa-star text-muted"></span>' + fav + '&nbsp;Favorite</label>';

                var now = moment();
                var time = moment(data[i].created_at);
                descriptions.push(desc);
                timeat.push(time.from(now));
                retweetby.push(retweeted);
                tweetsfav.push(str);

                var retweet = '';
                htmldata += '<div class="nowslide">' +
                        '<div class="col-md-12 col-xs-12 pad-0 bordered  ">' +
                        '<div class="retweet col-md-11 col-md-offset-1 col-sm-10 col-sm-offset-2 text-muted">' + retweeted + '</div>' +
                        '<div class="col-md-1 col-xs-2">' +
                        '<img src="' + profileimg + '" >' +
                        ' </div>' +
                        '<div class="col-md-11 col-xs-10 text-left ">' +
                        '<div class="col-md-12"><label class="bold text-muted ">' + username + '&nbsp;&nbsp;@' + screenname + '</label>' +
                        '<span class="pull-right text-muted">' +
                        time.from(now) +
                        '</span>' +
                        '</div>' +
                        '<div class="col-md-12 pull-left">' +
                        desc +
                        '</div>' +
                        media_url +
                        '<div class="col-md-12">' +
                        str +
                        '</div>' +
                        '</div>' +
                        '</div></div>';
            }

            $('.flexslider').empty();
            $('.flexslider').append('<div class="newslide"></div>');
            $('.newslide').append(htmldata);
            $('.flexslider').removeData("flexslider");
            $(".flexslider").flexslider({
                selector: '.newslide > .nowslide',
                animation: "slide",
                useCSS: false,
                controlNav: false,
                animationLoop: true,
                smoothHeight: true,
                touch: true,
                after: function (slider) {
                    $(window).resize();
                    $('.flexslider').resize();
                }
            });
            $('.flexslider').resize();
            $(window).resize();
            $('.flexslider').flexslider();
            finishload();

        }
        else {
            finishload();
        }


    }

    /*------------------------Get home tweet--------------------------*/
    $('#home').click(function () {
        homwtweet();
    });
    /*------------------------Get my tweet----------------------------*/
    $('#mytweet').click(function () {
        usertweet('me');
    });
    /*-------------------------Get user tweet-------------------------*/
    $('.tweet-user').click(function () {
        usertweet($(this).find('.screen-name').text());
    });

    /*---------------------Prepare file for download------------------*/
    $('.download').click(function () {
        $('#myModal').modal({backdrop: 'static'})

        /*-------Click on download buttom to download the file-----------------------------*/
        $('#downloadNow').click(function () {
            showError('File download successfully', 'alert-success');
            /*---Hide model after download the file-----*/
            $('#myModal').modal('hide');
        });

        $('#downloadNow').hide().attr('href', '');
        $('#loader').show();
        $('#myModal').modal('show');
        var d_type = $(this).attr('filetype');
        if (d_type == 'googleSheet') {
            document.location.href = 'redirect_Google.php?users=' + lastusers;
        }
        else if (d_type == 'csv' || d_type == 'json' || d_type == 'xls') {
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {
                    'type': d_type,
                    'users': lastusers
                },
                url: 'getFile.php',
                success: function (data) {
                    if (data.success == true) {
                        $('#downloadNow').attr('href', baseUrl + '/' + data.file);
$('.download-msg').text('Click download button to download file');
                        $('#loader').hide();
                        $('#downloadNow').show();
                    }
                },
                error: function (data, er, error) {
                    /*---Hide model on error-----*/
                    $('#myModal').modal('hide');
                    showError('Opps! Error in file download', 'alert-danger');
                }
            });

        }
        else if (d_type == 'xml') {
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {
                    'users': lastusers
                },
                url: 'getXml.php',
                success: function (data) {
                    if (data.success == true) {
                        $('#downloadNow').attr('href', baseUrl + '/' + data.file);
                        $('#loader').hide();
                        $('#downloadNow').show();
                    }
                },
                error: function (data, er, error) {
                    /*---Hide model on error-----*/
                    $('#myModal').modal('hide');
                    showError('Opps! Error in file download', 'alert-danger');
                }
            });

        }

    });

    /*-------------------show error in failure------------------------*/
    function showError(msg, type) {
        $('.operation-alert').removeClass('alert-warning alert-danger alert-success');
        $('.operation-alert .msg').html(msg);
        $('.operation-alert').addClass(type);
        $('.operation-alert').show();
        $('.operation-alert').delay(6000).slideUp(400);

    }

    /*-----------------Auto search followers if availble---------------*/
    var totalfollowers = parseInt($('followers').text());
    var options = {
        valueNames: ['name'],
        page: 12,
        i: Math.floor(Math.random() * (totalfollowers - 10))
    };
    var hackerList = new List('hacker-list', options);


    /*---------------------Finish or hide loader-----------------------*/
    function finishload() {
        $.unblockUI();
    }

    /*--------------------------Start loader---------------------------*/
    function strtLoad() {
        $.blockUI({css: {
                border: 'none',
                padding: '15px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity: .5,
                color: '#fff'
            }});

    }
    
    $('.googleAlert').delay(10000).slideUp(400);

})
