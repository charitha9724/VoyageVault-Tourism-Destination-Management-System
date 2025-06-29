<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    header("Location: log.php");
    exit();
}

include 'new.php'; // Ensure this connects to MySQL

$sql = "SELECT * FROM DESTINATION";
$result = $conn->query($sql);


// Image URLs mapping based on destination names
$image_urls = [
    "Munnar" => "https://images.roamaround.app/images/tea%20gardens%20munar.webp",
    "Goa" => "https://dynamic-media-cdn.tripadvisor.com/media/photo-o/15/33/fc/f0/goa.jpg?w=1600&h=800&s=1",
    "Lonavala" => "https://hblimg.mmtcdn.com/content/hubble/img/lonavaladestimgs/mmt/activities/t_ufs/m_Rajmachi_Fort_Lonavala_1_l_480_640.jpg",
    "Manali" => "https://www.citybit.in/wp-content/uploads/2024/09/Best-Time-to-Visit-Kullu-Manali.jpg",
    "Dandeli" => "https://cdn.abhibus.com/2024/06/Things-to-Do-in-in-Dandeli.jpg",
    "Zanskar Valley" => "https://indotoursadventures.com/public/storage/blogs/acaa30689f6667f3304e6f7b9c834e4f.jpeg",
    "Bir Billing" => "https://xcmag.com/wp-content/uploads/2019/09/Camp-360-Bir-Billing-Jerome-Maupoint.jpg",  
    "Madurai" =>"https://www.abhibus.com/blog/wp-content/uploads/2023/04/Madurai-Meenakshi-Temple-History-Timings-How-to-Reach.jpg",
    "Varanasi"=>"https://s7ap1.scene7.com/is/image/incredibleindia/manikarnika-ghat-city-hero?qlt=82&ts=1727959374496",
    "Agra"=>"https://upload.wikimedia.org/wikipedia/commons/thumb/1/1d/Taj_Mahal_%28Edited%29.jpeg/1200px-Taj_Mahal_%28Edited%29.jpeg",
    "Red Fort"=>"https://cdn.britannica.com/20/189820-050-D650A54D/Red-Fort-Old-Delhi-India.jpg",
    "Meghalaya"=>"https://www.tgetravels.com/wp-content/uploads/2024/05/Cover-Story-Meghalaya-.jpg",
    "Varkala"=>"https://www.keralatourism.org/images/microsites/varkala/varkala-1024x768.jpg",
    "Bandhavgarh National Park"=>"https://cdn.prod.website-files.com/65aa483b5ed7ebb96ac699bd/66d957b42aa4c7469f7a4e85_66d957058a9664bb0ecefc5d_HOR-11.jpeg",
    "Jim Corbett National Park"=>"https://dynamic-media-cdn.tripadvisor.com/media/photo-o/23/72/cb/f2/corbett-falla.jpg?w=900&h=500&s=1",

];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VoyageVault - Discover</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<?php include 'navbar.php'; ?>
<!-- Filter Section -->
<div class="container mt-4">
    <div class="row">
        <div class="col-md-3">
            <label for="costRange" class="form-label">Max Cost (₹)</label>
            <input type="range" class="form-range" min="3000" max="25000" step="500" id="costRange" value="15000" oninput="updateFilter()">
            <span id="costValue">₹25000</span>
        </div>
        <div class="col-md-3">
            <label for="categoryFilter" class="form-label">Category</label>
            <select class="form-select" id="categoryFilter" onchange="updateFilter()">
                <option value="">All Categories</option>
                <option value="Adventure">Adventure</option>
                <option value="Historical">Historical</option>
                <option value="Beach">Beach</option>
                <option value="Wildlife">Wildlife</option>
                <option value="Cultural">Cultural</option>
            </select>
        </div>
    </div>
</div>

<!-- JavaScript for Filtering based on searchbar, category, costrange-->
<script>
function updateFilter() {
    let searchQuery = document.getElementById('searchInput') ? document.getElementById('searchInput').value.trim() : "";
    let maxCost = document.getElementById('costRange').value;
    let category = document.getElementById('categoryFilter').value;

    document.getElementById("costValue").innerText = "₹" + maxCost;

    let xhr = new XMLHttpRequest();
    let params = new URLSearchParams({
        query: searchQuery,
        max_cost: maxCost,
        category: category
    });

    xhr.open("GET", "search_dst.php?" + params.toString(), true);

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                document.getElementById("destinationResults").innerHTML = xhr.responseText;
            } else {
                document.getElementById("destinationResults").innerHTML = "<p>Error loading destinations. Please try again later.</p>";
            }
        }
    };

    xhr.send();
}

</script>


<!-- Search Section -->
<div class="container text-center mt-5">
    <h1 class="fw-bold">Discover Amazing Destinations</h1>
    <input type="text" id="searchInput" class="form-control mt-3 w-50 mx-auto" placeholder="Search for destinations..." onkeyup="searchDestinations()">
</div>

<!-- Destination Cards -->
<div class="container mt-5">
    <div class="row" id="destinationResults">
        <?php while ($row = $result->fetch_assoc()) { 
            $destination_id = $row['destination_id'];
            $destination_name = $row['destination_name'];

            // Get image URL or use a default one
            $image_url = isset($image_urls[$destination_name]) ? $image_urls[$destination_name] : "https://images.roamaround.app/images/default.webp";

            // Fetch the actual average rating from the REVIEW table
            //$rating_query = "SELECT AVG(rating) AS avg_rating FROM REVIEW WHERE destination_id = $destination_id";
            //$rating_result = $conn->query($rating_query);
            //$rating_row = $rating_result->fetch_assoc();
            //$avg_rating = $rating_row['avg_rating'] ? number_format($rating_row['avg_rating'], 1) : "No ratings";

            // Fetch the actual average rating from the REVIEW table using prepared statement
            $avg_rating = "No ratings";

            $rating_stmt = $conn->prepare("SELECT AVG(rating) AS avg_rating FROM REVIEW WHERE destination_id = ?");
            $rating_stmt->bind_param("i", $destination_id);
            $rating_stmt->execute();
            $rating_result = $rating_stmt->get_result();

            if ($rating_result && $rating_row = $rating_result->fetch_assoc()) {
                if ($rating_row['avg_rating']) {
                    $avg_rating = number_format($rating_row['avg_rating'], 1);
                }
            }

            $rating_stmt->close();


            // Fetch places linked to this destination
            $places_query = "SELECT place_name FROM PLACES WHERE destination_id = $destination_id";
            $places_result = $conn->query($places_query);
            $places = [];
            while ($place_row = $places_result->fetch_assoc()) {
                $places[] = $place_row['place_name'];
            }
            $places_list = !empty($places) ? implode(", ", $places) : "No places available";

            // Fetch activities linked to this destination
            $activities_query = "SELECT DISTINCT a.activity_name 
                                 FROM ACTIVITIES a 
                                 JOIN ACTIVITIES_PLACES ap ON a.activity_id = ap.activity_id 
                                 JOIN PLACES p ON ap.place_id = p.place_id 
                                 WHERE p.destination_id = $destination_id";
            $activities_result = $conn->query($activities_query);
            $activities = [];
            while ($activity_row = $activities_result->fetch_assoc()) {
                $activities[] = $activity_row['activity_name'];
            }
            $activities_list = !empty($activities) ? implode(", ", $activities) : "No activities available";
        ?>
            <div class="col-md-4">
                <div class="card">
                    <img src="<?php echo $image_url; ?>" class="card-img-top" alt="<?php echo $destination_name; ?>">

                    <div class="card-body">
                        <h5 class="card-title"><?php echo $destination_name; ?>, <?php echo $row['state']; ?></h5>
                        <p class="card-text"><?php echo $row['description']; ?></p>
                        <span class="badge bg-warning text-dark">Rating: <?php echo $avg_rating; ?> ⭐</span>
                        <span class="badge bg-info text-dark">₹<?php echo number_format($row['min_cost']); ?> - ₹<?php echo number_format($row['max_cost']); ?></span>
                        <hr>
                        <p><strong>Places To Visit:</strong> <?php echo $places_list; ?></p>
                        <p><strong>Activities:</strong> <?php echo $activities_list; ?></p>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<!-- JavaScript for Search Functionality -->
<script>
function searchDestinations() {
    let searchQuery = document.getElementById('searchInput').value;
    
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "search_dst.php?query=" + searchQuery, true);
    
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById("destinationResults").innerHTML = xhr.responseText;
        }
    };

    xhr.send();
}
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php 
$result->free(); // Free result set
$conn->close(); // Close MySQL connection
?>
