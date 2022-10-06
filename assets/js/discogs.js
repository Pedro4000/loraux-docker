$(document).ready(function(){
    const howManyTabs = 5;
    const discogsArrayLenght = $('.discogs-query-control-pannel').data('elements')-1;
    let i = 0;
    let j= 0;
    let k= howManyTabs;
    let queryResult;
    let direction;
    let videosLinks = [];

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
        $('#discogs_response_object_name').html("Nom: "+discogsResponse[i].title);
    });

    $('.good-answer-ma-man').on('click',function(e){

        let type = $('.research-logo').attr('data-item-type');
        let id = $('.research-logo').attr('data-item-id');

        $.ajax({
            data: {
                discogsId : id,
                typeDiscogs : type
            },
            url: "/ajaxSaveReleasesInDB"
        }).done(function(response) {

            queryResult = response;
            console.log(response);

        });

    });

    $('.controls').on('click',function(e){
        console.log(videosArray);

    });


});
