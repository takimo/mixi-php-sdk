<div id="map_canvas" style="width:100%; height:100%"></div>

<script type="text/javascript">
var friendCheckinData = #{$data};
console.log(friendCheckinData);
function initialize() {
    var latlng = new google.maps.LatLng(35.608185,139.516754);
    var myOptions = {
      zoom: 10,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

    // marker
    for(var i=0; i<friendCheckinData.entry.length; i++){
        var data = friendCheckinData.entry[i];
        var locat = data.location;
        var user = data.user;

        var image = new google.maps.MarkerImage(user.thumbnailUrl);
        image.size = new google.maps.Size(50, 50);
        image.scaledSize = new google.maps.Size(50, 50);
        var myLatLng = new google.maps.LatLng(locat.latitude, locat.longitude);
        var beachMarker = new google.maps.Marker({
            position: myLatLng,
            map: map,
            icon: image
        });
    }
}
initialize();
</script>
