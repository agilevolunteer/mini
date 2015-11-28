$(function() {

    // just a super-simple JS demo

    var demoHeaderBox;

    // simple demo to show create something via javascript on the page
    if ($('#javascript-header-demo-box').length !== 0) {
    	demoHeaderBox = $('#javascript-header-demo-box');
    	demoHeaderBox
    		.hide()
    		.text('Hello from JavaScript! This line has been added by public/js/application.js')
    		.css('color', 'green')
    		.fadeIn('slow');
    }

    // if #javascript-ajax-button exists
    if ($('#javascript-ajax-button').length !== 0) {

        $('#javascript-ajax-button').on('click', function(){

            // send an ajax-request to this URL: current-server.com/songs/ajaxGetStats
            // "url" is defined in views/_templates/footer.php
            $.ajax(url + "/songs/ajaxGetStats")
                .done(function(result) {
                    // this will be executed if the ajax-call was successful
                    // here we get the feedback from the ajax-call (result) and show it in #javascript-ajax-result-box
                    $('#javascript-ajax-result-box').html(result);
                })
                .fail(function() {
                    // this will be executed if the ajax-call had failed
                })
                .always(function() {
                    // this will ALWAYS be executed, regardless if the ajax-call was success or not
                });
        });
    }

    if ($('#vie-test-results').length !== 0) {
        $.get(url + "/speed/getEnhanced Results", function(data){
            for (key in data.indicators){
                var indicator = data.indicators[key];

                var resultContainer = $('#vie-test-results').append("<div class='vie-test-result-container'/>");
                resultContainer.append("<h3>"+ key +"</h3>");
                var itemContainer = $("<div class='vie-test-result-item'/>");
                resultContainer.append(itemContainer);
                var dataset = new vis.DataSet(indicator);
                var startDate = new Date(indicator[0].x)
                var endDate = new Date(indicator[indicator.length-1].x)

                startDate.setDate(startDate.getDate() - 1);
                endDate.setDate(endDate.getDate() + 1);

                options = {
                    start: startDate,
                    end: endDate
                }

                var graph2d = new vis.Graph2d(itemContainer[0], dataset, options);

            }
            


/*            var render = [];
            var speedIndex = [];
            data.forEach(function(item){
                console.log(item.completed, item.speedIndex);
                render.push({
                    x: item.completed,
                    y: item.render

                });

                speedIndex.push({
                    x: item.completed,
                    y: item.speedIndex

                });
            });

            var container = document.getElementById('vie-test-results--render');

            var container2 = document.getElementById('vie-test-results--speed-index');
            options = {
                start: '2015-11-06',
                end: '2015-11-11'
            }

            var dataset = new vis.DataSet(render);
            var dataset2 = new vis.DataSet(speedIndex);
            var graph2d = new vis.Graph2d(container, dataset, options);
            var graph2d2 = new vis.Graph2d(container2, dataset2, options);*/
        });
    }

});
