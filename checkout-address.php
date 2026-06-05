<?php

session_start();

if(!isset($_SESSION['user_id'])){

    $_SESSION['checkout_redirect'] = true;

    header("Location: login.php");

    exit();

}

$cart = $_SESSION['cart'] ?? [];

$total = 0;

foreach($cart as $item){

    $total += ((float)$item['price']) * $item['quantity'];

}

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Address | TrustFund</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:#f5f5f5;
    font-family:Arial,sans-serif;
    color:#111;
}

/* PAGE */

.page-wrapper{
    max-width:1200px;
    margin:auto;
    padding:40px 20px 80px;
}

/* TITLE */

.page-title{
    font-size:32px;
    font-weight:800;
    margin-bottom:35px;
}

/* STEPS */

.steps{
    display:flex;
    align-items:center;
    justify-content:center;
    margin-bottom:45px;
}

.step{
    display:flex;
    flex-direction:column;
    align-items:center;
    font-size:11px;
    font-weight:700;
    color:#999;
}

.step.active{
    color:#9b59b6;
}

.step-circle{
    width:24px;
    height:24px;
    border-radius:50%;
    background:#9b59b6;
    color:white;

    display:flex;
    align-items:center;
    justify-content:center;

    font-size:11px;
    margin-bottom:8px;
}

.step-line{
    width:130px;
    height:4px;
    background:#d9b6ea;
    margin:0 12px;
    border-radius:999px;
}

/* LAYOUT */

.checkout-layout{
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:24px;
}

/* LEFT */

.address-section{
    background:white;
    border:1px solid #eee;
    padding:24px;
}

/* TITLES */

.section-title{
    font-size:18px;
    font-weight:800;
    margin-bottom:18px;
}

.section-subtitle{
    font-size:13px;
    color:#777;
    margin-bottom:20px;
}

/* LOCATION */

.location-row{
    display:flex;
    align-items:center;
    gap:18px;
    margin-bottom:20px;
}

.location-select{
    width:240px;
    padding:12px;
    border:1px solid #ddd;
    font-size:13px;
}

.location-link{
    color:#9b59b6;
    font-size:13px;
    font-weight:700;
    cursor:pointer;
}

/* PROVINCE */

.province-title{
    font-size:14px;
    font-weight:800;
    margin-bottom:18px;
}

/* ADDRESS CARD */

.address-card{
    border:1px solid #ddd;
    padding:18px;
    margin-bottom:14px;
    display:block;
}

.address-left{
    display:flex;
    gap:14px;
}

.address-radio{
    margin-top:5px;
}

.address-name{
    font-size:13px;
    font-weight:800;
    margin-bottom:4px;
}

.address-text{
    font-size:12px;
    color:#666;
    line-height:1.6;
}

.more-info{
    color:#9b59b6;
    font-size:12px;
    font-weight:700;
}

/* PAGINATION */

.pagination-box{
    display:flex;
    gap:8px;
    margin-top:30px;
}

.page-btn{
    width:32px;
    height:32px;
    border:1px solid #ddd;
    background:white;
    font-size:12px;
}

.page-btn.active{
    background:#9b59b6;
    color:white;
    border-color:#9b59b6;
}

/* RIGHT */

.summary-box{
    background:white;
    border:1px solid #eee;
    padding:24px;
    height:fit-content;
}

.summary-title{
    font-size:16px;
    font-weight:800;
    margin-bottom:22px;
}

.summary-row{
    display:flex;
    justify-content:space-between;
    font-size:13px;
    margin-bottom:16px;
}

.summary-total{
    border-top:1px solid #eee;
    margin-top:18px;
    padding-top:18px;

    display:flex;
    justify-content:space-between;

    font-size:16px;
    font-weight:800;
}

.delivery-date{
    margin-top:16px;
    font-size:12px;
    color:#777;
}

.coupon-box{
    width:100%;
    padding:12px;
    border:1px solid #ddd;
    margin-top:18px;
    font-size:12px;
}

.checkout-btn{
    width:100%;
    border:none;
    background:#9b59b6;
    color:white;
    padding:14px;
    margin-top:18px;
    font-size:12px;
    font-weight:700;
}

@media(max-width:900px){

    .checkout-layout{
        grid-template-columns:1fr;
    }

}
/* CARD TOP */

.card-top{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    width:100%;
}

/* EXTRA INFO */

.extra-info{
    width:100%;
    margin-top:18px;
    border-top:1px solid #eee;
    padding-top:18px;
}

.map-box{
    width:100%;
    border-radius:12px;
    overflow:hidden;
    margin-bottom:18px;
}

.map-box iframe{
    width:100%;
    display:block;
}

/* BUSINESS HOURS */

.business-hours{
    font-size:12px;
}

.hours-title{
    font-weight:800;
    margin-bottom:14px;
    line-height:1.6;
}

.hours-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:12px;
    color:#555;
}
.card-top{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:20px;
    width:100%;
}

.extra-info{
    width:100%;
    margin-top:18px;
    border-top:1px solid #eee;
    padding-top:18px;
}

.map-box{
    width:100%;
    border-radius:12px;
    overflow:hidden;
    margin-bottom:18px;
}

.map-box iframe{
    width:100%;
    height:260px;
    display:block;
    border:0;
}
</style>

</head>

<body>

<div class="page-wrapper">

<h1 class="page-title">
Shopping cart
</h1>

<!-- STEPS -->

<div class="steps">

<div class="step active">

<div class="step-circle">1</div>

<div>MY CART</div>

</div>

<div class="step-line"></div>

<div class="step active">

<div class="step-circle">2</div>

<div>ADDRESS</div>

</div>

<div class="step-line"></div>

<div class="step">

<div class="step-circle">3</div>

<div>PAYMENT</div>

</div>

</div>

<div class="checkout-layout">

<!-- LEFT -->

<!-- LEFT -->

<form method="POST" action="checkout-review.php">

<div class="address-section">

<div class="section-title">
Collection point
</div>

<div class="section-subtitle">
Find a Pick up point closest to you.
</div>

<!-- LOCATION -->

<div class="location-row">

<select class="location-select" id="provinceSelect">

<option value="">
Available provinces
</option>

<option value="Eastern Cape">
Eastern Cape
</option>

<option value="Free State">
Free State
</option>

<option value="Gauteng">
Gauteng
</option>

<option value="KwaZulu-Natal">
KwaZulu-Natal
</option>

<option value="Limpopo">
Limpopo
</option>

<option value="Mpumalanga">
Mpumalanga
</option>

<option value="North West">
North West
</option>

<option value="Northern Cape">
Northern Cape
</option>

<option value="Western Cape">
Western Cape
</option>

</select>

<div class="location-link" onclick="getLocation()">
Use my location
</div>

</div>

<!-- DYNAMIC PROVINCE -->

<div class="province-title" id="provinceTitle">
Nearby Collection Points
</div>

<div id="collectionPoints"></div>

<!-- PAGINATION -->

<div class="pagination-box" id="paginationBox">

<button class="page-btn active">
1
</button>

<button class="page-btn">
2
</button>

<button class="page-btn">
3
</button>

<button class="page-btn">
4
</button>

</div>

</div>
</div>
</div>

<!-- RIGHT -->

<div class="summary-box">

<div class="summary-title">
BILLING DETAILS
</div>

<div class="summary-row">

<div>Cart Total</div>

<div>
R<?php echo number_format($total); ?>
</div>

</div>

<div class="summary-row">

<div>Shipping Charges</div>

<div style="color:#9b59b6;">
Free
</div>

</div>

<div class="summary-row">

<div>Coupon Applied</div>

<div>R0.00</div>

</div>

<div class="summary-total">

<div>TOTAL</div>

<div>
R<?php echo number_format($total); ?>
</div>

</div>

<div class="delivery-date">

Estimated Delivery by<br>

<?php

echo date(

    "d M, Y",

    strtotime("+1 day")

);

?>

</div>

<input 
type="text"
placeholder="Coupon Code"
class="coupon-box"
>

<a href="checkout-review.php">

<button class="checkout-btn">

Proceed to Checkout

</button>

</a>

</div>

</div>

</div>

<script>

const locations = {

    "Eastern Cape":[

        {name:"Chiselhurst", city:"East London", address:"28 Manchester Road"},
        {name:"Beacon Bay", city:"East London", address:"45 Bonza Bay Road"},
        {name:"Vincent", city:"East London", address:"12 Western Avenue"},
        {name:"Mthatha Plaza", city:"Mthatha", address:"55 Nelson Mandela Drive"},
        {name:"Walmer", city:"Gqeberha", address:"89 Main Road"},
        {name:"Greenacres", city:"Gqeberha", address:"Cape Road"},
        {name:"Mdantsane City", city:"East London", address:"Village 1"},
        {name:"King Williams Town", city:"Qonce", address:"27 Alexandra Road"},
        {name:"Jeffreys Bay Centre", city:"Jeffreys Bay", address:"Da Gama Road"},
        {name:"Gonubie", city:"East London", address:"Main Road"},
        {name:"Port Alfred", city:"Port Alfred", address:"Settlers Way"},
        {name:"Queenstown Central", city:"Queenstown", address:"Komani Street"}

    ],

    "Gauteng":[

        {name:"Sandton", city:"Johannesburg", address:"12 Rivonia Road"},
        {name:"Rosebank", city:"Johannesburg", address:"50 Oxford Road"},
        {name:"Fourways Mall", city:"Johannesburg", address:"11 Ruby Close"},
        {name:"Midrand", city:"Johannesburg", address:"22 Old Pretoria Road"},
        {name:"Menlyn", city:"Pretoria", address:"88 Atterbury Road"},
        {name:"Centurion Mall", city:"Centurion", address:"126 Heuwel Road"},
        {name:"Mall of Africa", city:"Midrand", address:"Magwa Crescent"},
        {name:"Kempton Park", city:"Johannesburg", address:"Festival Mall"},
        {name:"Pretoria CBD", city:"Pretoria", address:"45 Church Street"},
        {name:"Soweto", city:"Johannesburg", address:"Maponya Mall"},
        {name:"Randburg", city:"Johannesburg", address:"Republic Road"},
        {name:"East Rand Mall", city:"Boksburg", address:"Bentel Avenue"},
        {name:"Benoni Lakeside", city:"Benoni", address:"Tom Jones Street"},
        {name:"Alberton City", city:"Alberton", address:"Voortrekker Road"},
        {name:"Hatfield", city:"Pretoria", address:"Burnett Street"},
        {name:"Bryanston", city:"Johannesburg", address:"Main Road"},
        {name:"Bedfordview", city:"Johannesburg", address:"Van Buuren Road"},
        {name:"Silver Lakes", city:"Pretoria", address:"Graham Road"}

    ],

    "Western Cape":[

        {name:"Sea Point", city:"Cape Town", address:"18 Beach Road"},
        {name:"Claremont", city:"Cape Town", address:"77 Main Road"},
        {name:"Canal Walk", city:"Cape Town", address:"Century Boulevard"},
        {name:"Stellenbosch", city:"Stellenbosch", address:"16 Andringa Street"},
        {name:"Somerset Mall", city:"Somerset West", address:"N2 & R44"},
        {name:"V&A Waterfront", city:"Cape Town", address:"Dock Road"},
        {name:"Bellville", city:"Cape Town", address:"Voortrekker Road"},
        {name:"Paarl Central", city:"Paarl", address:"Main Street"},
        {name:"George Mall", city:"George", address:"York Street"},
        {name:"Mossel Bay", city:"Mossel Bay", address:"Marsh Street"},
        {name:"Hermanus", city:"Hermanus", address:"Harbour Road"},
        {name:"Worcester Central", city:"Worcester", address:"High Street"}

    ],

    "KwaZulu-Natal":[

        {name:"Umhlanga", city:"Durban", address:"9 Lagoon Drive"},
        {name:"Gateway Mall", city:"Durban", address:"1 Palm Boulevard"},
        {name:"Ballito Junction", city:"Ballito", address:"Leonora Drive"},
        {name:"Pietermaritzburg CBD", city:"Pietermaritzburg", address:"34 Church Street"},
        {name:"Musgrave Centre", city:"Durban", address:"Musgrave Road"},
        {name:"Chatsworth Centre", city:"Durban", address:"Joyhurst Street"},
        {name:"Richards Bay Plaza", city:"Richards Bay", address:"Bullion Boulevard"},
        {name:"Newcastle Mall", city:"Newcastle", address:"Allen Street"},
        {name:"Scottburgh", city:"Scottburgh", address:"Marine Drive"},
        {name:"Empangeni Central", city:"Empangeni", address:"Tanner Road"},
        {name:"Umlazi Mega City", city:"Durban", address:"Mangosuthu Highway"},
        {name:"Hillcrest", city:"Durban", address:"Old Main Road"}

    ],

    "Free State":[

        {name:"Bloemfontein Central", city:"Bloemfontein", address:"14 Maitland Street"},
        {name:"Mimosa Mall", city:"Bloemfontein", address:"131 Kellner Street"},
        {name:"Welkom Square", city:"Welkom", address:"Stateway"},
        {name:"Kroonstad Centre", city:"Kroonstad", address:"Cross Street"},
        {name:"Bethlehem Plaza", city:"Bethlehem", address:"Preekstoel Road"},
        {name:"Sasolburg City", city:"Sasolburg", address:"John Vorster Road"}

    ],

    "Limpopo":[

        {name:"Polokwane CBD", city:"Polokwane", address:"22 Market Street"},
        {name:"Mall of the North", city:"Polokwane", address:"R81 Road"},
        {name:"Thohoyandou Centre", city:"Thohoyandou", address:"Phangami Mall"},
        {name:"Tzaneen Lifestyle", city:"Tzaneen", address:"Agatha Street"},
        {name:"Mokopane Junction", city:"Mokopane", address:"Nelson Mandela Drive"},
        {name:"Lephalale Mall", city:"Lephalale", address:"Chris Hani Drive"}

    ],

    "Mpumalanga":[

        {name:"Mbombela Crossing", city:"Nelspruit", address:"Samora Machel Drive"},
        {name:"eMalahleni Centre", city:"Witbank", address:"45 Mandela Street"},
        {name:"Secunda Mall", city:"Secunda", address:"Oliver Tambo Drive"},
        {name:"White River", city:"White River", address:"Chief Mgiyeni Khumalo Drive"},
        {name:"Middelburg Mall", city:"Middelburg", address:"Walter Sisulu Street"},
        {name:"Sabie Central", city:"Sabie", address:"Main Road"}

    ],

    "North West":[

        {name:"Rustenburg Mall", city:"Rustenburg", address:"45 Fatima Bhayat Street"},
        {name:"Potchefstroom CBD", city:"Potchefstroom", address:"10 Walter Sisulu Avenue"},
        {name:"Klerksdorp City", city:"Klerksdorp", address:"Margaretha Prinsloo Street"},
        {name:"Mahikeng Plaza", city:"Mahikeng", address:"University Drive"},
        {name:"Brits Centre", city:"Brits", address:"Spoorweg Street"},
        {name:"Vryburg Junction", city:"Vryburg", address:"Market Street"}

    ],

    "Northern Cape":[

        {name:"Kimberley Centre", city:"Kimberley", address:"22 Du Toitspan Road"},
        {name:"Upington Mall", city:"Upington", address:"Schroder Street"},
        {name:"Kuruman Junction", city:"Kuruman", address:"Main Road"},
        {name:"Springbok Centre", city:"Springbok", address:"Voortrekker Street"},
        {name:"De Aar Plaza", city:"De Aar", address:"Station Road"}

    ]

};
let currentProvince = "";

let currentPage = 1;

const itemsPerPage = 6;

/* LOAD LOCATIONS */

function loadLocations(province = "", page = 1){

    currentProvince = province;

    currentPage = page;

    const container = document.getElementById("collectionPoints");

    const title = document.getElementById("provinceTitle");

    const pagination = document.getElementById("paginationBox");

    container.innerHTML = "";

    pagination.innerHTML = "";

    /* ALL PROVINCES */

    let provinceLocations = [];

    if(province === ""){

        title.innerHTML = "All Collection Points";

        Object.values(locations).forEach(locationArray => {

            provinceLocations = [

                ...provinceLocations,

                ...locationArray

            ];

        });

    }

    /* SINGLE PROVINCE */

    else{

        title.innerHTML = province;

        provinceLocations = locations[province] || [];

    }

    /* PAGINATION */

    const start = (page - 1) * itemsPerPage;

    const end = start + itemsPerPage;

    const paginatedLocations = provinceLocations.slice(start, end);

    /* RENDER */

    paginatedLocations.forEach((location, index) => {

        container.innerHTML += `

        <div class="address-card">

    <div class="card-top">

    <div class="address-left">

               <input 
               type="radio"
               name="pickup_point"
               value="${location.name} - ${location.city}"
               ${index === 0 ? "checked" : ""}
                class="address-radio"
>

                <div>

                    <div class="address-name">

                        ${location.name}

                    </div>

                    <div class="address-text">

                        ${location.city}<br>

                        ${location.address}

                    </div>

                </div>

            </div>
<div 
class="more-info"
onclick="toggleInfo(
this,
'${location.name}',
'${location.city}',
'${location.address}'
)"
>
    More Info
</div>

        </div>
		</div>

        `;

    });

    /* PAGE BUTTONS */

    const totalPages = Math.ceil(

        provinceLocations.length / itemsPerPage

    );

    for(let i = 1; i <= totalPages; i++){

        pagination.innerHTML += `

        <button 
        class="page-btn ${i === page ? "active" : ""}"
        onclick="loadLocations('${province}', ${i})"
        >

        ${i}

        </button>

        `;

    }

}

/* DROPDOWN */

document.getElementById("provinceSelect").addEventListener(

    "change",

    function(){

        loadLocations(this.value, 1);

    }

);

/* GEOLOCATION */

function getLocation(){

    if(navigator.geolocation){

        navigator.geolocation.getCurrentPosition(

            async function(position){

                const lat = position.coords.latitude;
                const lon = position.coords.longitude;

                try{

                    const response = await fetch(

                        `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`

                    );

                    const data = await response.json();

                    const state = data.address.state || "";

                    const provinceSelect = document.getElementById("provinceSelect");

                    if(state.includes("Gauteng")){

                        provinceSelect.value = "Gauteng";

                        loadLocations("Gauteng", 1);

                    }

                    else if(state.includes("Eastern Cape")){

                        provinceSelect.value = "Eastern Cape";

                        loadLocations("Eastern Cape", 1);

                    }

                    else if(state.includes("Western Cape")){

                        provinceSelect.value = "Western Cape";

                        loadLocations("Western Cape", 1);

                    }

                    else if(state.includes("KwaZulu-Natal")){

                        provinceSelect.value = "KwaZulu-Natal";

                        loadLocations("KwaZulu-Natal", 1);

                    }

                    else{

                        alert("Province not supported yet.");

                    }

                }

                catch(error){

                    alert("Could not detect province.");

                }

            },

            function(){

                alert("Location permission denied.");

            }

        );

    }

}

/* DEFAULT LOAD */

loadLocations("", 1);
function toggleInfo(element, name, city, address){

    const existing = element
    .parentElement
    .querySelector(".extra-info");

    if(existing){

        existing.remove();

        element.innerHTML = "More Info";

        return;

    }

    element.innerHTML = "Less Info";

    const locationQuery = `${name} ${city} South Africa`;

    const info = document.createElement("div");

    info.className = "extra-info";

    info.innerHTML = `

    <div class="map-box">

        <iframe
        width="100%"
        height="220"
        frameborder="0"
        style="border:0"
        src="https://maps.google.com/maps?q=${encodeURIComponent(locationQuery)}&t=&z=15&ie=UTF8&iwloc=&output=embed"
        allowfullscreen>
        </iframe>

    </div>

    <div class="business-hours">

        <div class="hours-title">

            ${name}<br>

            ${address}<br>

            ${city}, South Africa

        </div>

        <div class="hours-grid">

            <div>Monday</div>
            <div>08H00 - 19H00</div>

            <div>Tuesday</div>
            <div>08H00 - 19H00</div>

            <div>Wednesday</div>
            <div>08H00 - 19H00</div>

            <div>Thursday</div>
            <div>08H00 - 19H00</div>

            <div>Friday</div>
            <div>08H00 - 19H00</div>

            <div>Saturday</div>
            <div>09H00 - 17H00</div>

        </div>

    </div>

    `;

  element.closest(".address-card").appendChild(info);

}
</script>

</body>
</html>