$(function() {

    $(document).on('click','.custom-drop-down',function(e){

        if ($('.custom-drop-down-list').is(':visible'))
        {
            $(this).find(".custom-drop-down-list").slideUp(200);
        }
        else
        {
            $(this).find(".custom-drop-down-list").slideDown(200);


        }

        e.stopPropagation();
    });


    function feedDropDown(it) {

        var ul = $("#cards-drop.custom-drop-down").find("ul");

        for (i = 0; i < it.length; i++) {
            var li = document.createElement("li");
            li.innerHTML = it[i]
            ul.append(li);
        }
    }



    //write Code onValueChange
    function onValueChange(that) {
        /*ON change called for cards */
        if ($(that).id == "cards-drop") {

            document.getElementById("cards").innerHTML = "";
            createRows(darkText2);

        }
        else
        {
            /*ON change called for meat selection */
            $(".expandable").slideDown();
            $("#main-popup").css("transform" ,"translate(-50%,-65%)")
        }

    }



    function createRows(items) {
        var card = document.getElementById("cards");

        var row;


        for (i = 0; i < items.length; i++) {

            var colcontainer = document.createElement("div");
            colcontainer.className = "cci-col-50";

            var newRow = i % 2 == 1 || i == 0;
            colcontainer.appendChild(createCard("img/Image2.png", items[i], "75 NIS", datafromServer1[i]));

            if (newRow) {
                row = document.createElement("div");
                row.className = "cci-row"
            }

            row.appendChild(colcontainer);

            if (!newRow || items.length - 1 == i) {
                card.appendChild(row)
            }

        }

    }

    function createCard(imgUrl, boldText, badge, dimDetail) {

        var card = document.createElement("div");
        card.className = "product-card";

        var cardImg = document.createElement("img");

        var cardBody = document.createElement("div");
        cardBody.className = "card-body";

        var cardHeader = document.createElement("div");
        cardHeader.className = "header";

        var darkSpan = document.createElement("span");
        darkSpan.className = "dark-text";

        var cciBadge = document.createElement("span");
        cciBadge.className = "cci-badge";

        var dimText = document.createElement("div");
        dimText.className = "dim-text";

        /*Set data here*/
        darkSpan.innerHTML = boldText;
        cardImg.src = imgUrl;
        cciBadge.innerHTML = badge;
        dimText.innerHTML = dimDetail;

        cardHeader.appendChild(darkSpan);
        cardHeader.appendChild(cciBadge);

        cardBody.appendChild(cardHeader)
        cardBody.appendChild(dimText)

        card.appendChild(cardImg);
        card.appendChild(cardBody);

        return card;

    }


    //Uncomment e.stopPropagation()
    //in case of stop toggling when clicked on li Item
    $(document).on('click','.custom-drop-down-list ul',function(e){

        e.stopPropagation();
    });



    $("body").on("click", function() {


        $(".custom-drop-down-list").slideUp(200);

    });


})