$(document).ready(function () {

    /* =============== For the discogs research slider =============== */

    let i = 0;
    let j = 0;
    let queryResult;
    let direction;
    let videosLinks = [];
    
    $('.discogs-research-button').on('click', function (e) {
        let  discogsArrayLenght = discogsArrayLenght ? discogsArrayLenght : $('.discogs-query-control-pannel').data('elements') - 1;
        var discogsResponse = discogsResponse ? discogsResponse : $('#discogs-data-div').data('response_discogs').results;

        direction = e.target.className.split('-')[0];
        if (direction == 'next') {
            if (i == discogsArrayLenght) {
                i = 0;
            } else {
                i++;
            }
        } else {
            if (i == 0) {
                i = discogsArrayLenght;
            } else {
                i--;
            }
        }
        $('.research-logo').attr('src', discogsResponse[i].cover_image);
        $('.span-discogs-attributes').attr('data-item-type', discogsResponse[i].type);
        $('.span-discogs-attributes').attr('data-item-id', discogsResponse[i].id);
        $('.dj-or-label').html("Juste pour info c'est un " + discogsResponse[i].type);
        $('#discogs_response_object_name').html("Nom: " + discogsResponse[i].title);
    });

    /* ===================== call for scrap function ===================== */

    /* ====== In the landing page ==========*/
    $('.good-answer-ma-man').on('click', function (e) {

        let type = $('.discogs-attributes').attr('data-item-type');
        let id = $('.discogs-attributes').attr('data-item-id');
        $.ajax({
            data: {
                discogsId: id,
                typeDiscogs: type
            },
            url: "/ajaxSaveReleasesInDB",
        }).done(function (response) {
            queryResult = response;
            console.log(response);
        });
    });

    /* ======== In the label/artist : index page ======== */
    $('.discogs-scrap').on('click', function(){
        let type = $(this).attr('data-item-type');
        var discogsId = $(this).attr('data-item-id');
        $.ajax({
            data: {
                discogsId: discogsId,
                typeDiscogs: type
            },
            url: "/ajaxSaveReleasesInDB",
            beforeSend: function(){
                $("#scroll-scrap-"+discogsId).addClass('d-none');
                $("#loader-scrap-"+discogsId).removeClass('d-none');
            }
        }).done(function (response) {
            queryResult = response;
            var discogsObject = response.discogsObject;
            console.log(discogsObject, discogsObject.numberScapped);
            $("#scroll-scrap-"+discogsId).removeClass('d-none');
            $("#loader-scrap-"+discogsId).addClass('d-none');
            $("#index-progression-"+discogsId).html(discogsObject.numberScrapped+'/'+discogsObject.totalItems);
            $("#index-scrapped-"+discogsId).html('true');
            $("#index-scrapped-date-"+discogsId).html(discogsObject.fullyScrappedDate);
            $("#index-videos-link-"+discogsId).find("a").removeClass("d-none");
        });
    });


    /*============== Here for controlling the youtube iframe source in videos ============== */ 
    var videosData = $('#videos-data').data('video');
    var index;

    $('.previous-video').on('click', function (e) {
        index = ($('#video-counter').data('index') == 0) ? $('#video-counter').data('videos-length')-1 : ($('#video-counter').data('index')-1);
        changeVideo(index);
    });
    $('.next-video').on('click', function (e) {
        index = ($('#video-counter').data('index') == $('#video-counter').data('videos-length')-1) ? 0 : ($('#video-counter').data('index')+1);
        changeVideo(index);
    });

    $('.video-list').on('click', function(e){
        index = parseInt(this.getAttribute('data-index'));
        changeVideo(index);
    });

    function changeVideo(index) {
        $('#video-counter').data('index', index);
        $('.video-list.active').removeClass('active');
        $(".video-list[data-index='"+index+"']").addClass('active');
        let uri = "https://www.youtube.com/embed/"+(videosData[index].youtubeId)+"?autoplay=1&origin=https://localhost";
        $('#ytplayer').attr('src', uri);
        let html = 'video '+(index+1)+'/'+($('#video-counter').data('videos-length'));
        $('#video-counter').html(html);
    }

    /* ================ Ajax for label deletion ================ */

    $('.discogs-scrap').on('click', function(){
        var id = $(this).attr('data-item-id');
        console.log(id);
        $.ajax({
            method: 'delete',
            data: {
                id: labelId,
            },
            url: "/music/label/delete",
        }).done(function (response) {
            if (response.code == 200) {
                console.log(response, 'ok');
            }
        });
    });


});
