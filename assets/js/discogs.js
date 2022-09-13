$(document).ready(function(){
    const howManyTabs = 5;
    const discogsArrayLenght = $('.discogs-query-control-pannel').data('elements')-1;
    let i = 0;
    let j= 0;
    let k= howManyTabs;
    let queryResult;
    let direction;
    let videosLinks = [];

    //var discogsResponse = $('#discogs-data-div').data('response_discogs').results;
    $('.discogs-research-button').on('click',function(e){

        direction = e.target.className.split('-')[0];
        if (direction=='next') {
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
        $('.research-logo').attr('src',discogsResponse[i].cover_image);
        $('.research-logo').attr('data-item-type',discogsResponse[i].type);
        $('.research-logo').attr('data-item-id',discogsResponse[i].id);
        $('.dj-or-label').html("Juste pour info c'est un "+discogsResponse[i].type);
    });

    $('.good-answer-ma-man').on('click',function(e){

        let type = $('.research-logo').attr('data-item-type');
        let id = $('.research-logo').attr('data-item-id');

        $.ajax({
            data: {
                id:id,
                type:type
            },
            url: "/ajaxLoadVideos"
        }).done(function(response) {
            queryResult = response;
/*
            $('.video-section').append('<p><a href="http://127.0.0.1:8000/createYoutubePlaylist">Créer la playlist</a> </p>')
*/
            for (i = 0; i < 5; i++) {
                videosLinks.push(queryResult[1][i]);
            }
            $('.video-section').append('<p><button type="button" class="open-links-in-new-tab btn btn-light" data-uri='+videosLinks+'>Ouvrir cinq onglets</button> </p>')
            console.log(queryResult);
        });



    $(document).on('click','.open-links-in-new-tab',function(e){
        console.log($('.open-links-in-new-tab').data("uri"));
        let videosArray = $('.open-links-in-new-tab').data('uri');
        let l;
        let m=1;
        j+=5;
        k+=5;
        videosArray = videosArray.split(',');
        console.log(videosArray.length);
       for (i=0; i < videosArray.length; i++) {
           win = window.open(videosArray[i]);
           win.blur()
           window.focus();
       }
        m=1;
        videosLinks=[];
        for (l = j; l < k ; l++) {
            if (m==1) {
                videosLinks= queryResult[1][l]+',';
                m=2;
                continue;
            }
            if (l == k-1) {
                videosLinks = videosLinks+queryResult[1][l];
            }else{
                videosLinks = videosLinks+queryResult[1][l]+',';
            }
            $('.open-links-in-new-tab').data("uri",videosLinks);
        }
    });

        $videosss = {
            0 : "",
            1 :{
                0 :
                {'artists' : "Paul Cut",
                'videoName' : "Paul Cut - The Joy",
                'videoUri' : "https://www.youtube.com/watch?v=8lv2hQZVHes"},
                1:
                {'artists' : "Paul Cut",
                'videoName' : "Paul Cut - The World",
                'videoUri' : "https://www.youtube.com/watch?v=BUFdKuW-QEE"}
            }
        }


    });


});
