$(document).ready(function () {

    /* =============== For the discogs research slider =============== */

    const discogsArrayLenght = $('.discogs-query-control-pannel').data('elements') - 1;
    let i = 0;
    let j = 0;
    let queryResult;
    let direction;
    let videosLinks = [];
    var discogsResponse = $('#discogs-data-div').data('response_discogs').results;
 
    $('.discogs-research-button').on('click', function (e) {

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
            url: "/ajaxSaveReleasesInDB"
        }).done(function (response) {
            queryResult = response;
            console.log(response);
        });
    });

    /* ======== In the label/artist : index page ======== */
    $('.discogs-scrap').on('click', function(){
        console.log($(this).closest('span'));
        let span = $(this).closest('span');
        let type = span.attr('data-item-type');
        let id = span.attr('data-item-id');
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
        //$('#ytplayer').attr('src', uri);
        let html = 'video '+(index+1)+'/'+($('#video-counter').data('videos-length'));
        $('#video-counter').html(html);
    }

    
    


});
