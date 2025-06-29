<?php
include 'new.php';
// Debug code


// Get search filters from GET request
$searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';
$maxCost = isset($_GET['max_cost']) ? (float)$_GET['max_cost'] : 25000;
$category = isset($_GET['category']) ? trim($_GET['category']) : '';



// Base SQL Query
$sql = "SELECT * FROM DESTINATION WHERE (destination_name LIKE ? OR state LIKE ?) AND max_cost <= ?";
$params = ["%$searchQuery%", "%$searchQuery%", $maxCost];
$types = "ssd";

// Apply Category Filter
if (!empty($category)) {
    //$sql .= " AND category = ?";
    $sql .= " AND LOWER(category) = LOWER(?)";
    $params[] = $category;
    $types .= "s";
}

// Prepare and execute statement
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Image URLs
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

// Display results
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $destination_id = $row['destination_id'];
        $destination_name = $row['destination_name'];
        $image_url = $image_urls[$destination_name] ?? "https://images.roamaround.app/images/default.webp";

        // Fetch Average Rating (NEW CODE)
        $rating_query = "SELECT AVG(rating) AS avg_rating FROM REVIEW WHERE destination_id = ?";
        $rating_stmt = $conn->prepare($rating_query);
        $rating_stmt->bind_param("i", $destination_id);
        $rating_stmt->execute();
        $rating_result = $rating_stmt->get_result();
        $rating_row = $rating_result->fetch_assoc();
        $average_rating = $rating_row['avg_rating'] ? round($rating_row['avg_rating'], 1) : "No ratings"; 

        // Fetch places
        $places_query = "SELECT place_name FROM PLACES WHERE destination_id = ?";
        $places_stmt = $conn->prepare($places_query);
        $places_stmt->bind_param("i", $destination_id);
        $places_stmt->execute();
        $places_result = $places_stmt->get_result();
        $places = [];
        while ($place_row = $places_result->fetch_assoc()) {
            $places[] = $place_row['place_name'];
        }
        $places_list = !empty($places) ? implode(", ", $places) : "No places available";

        // Fetch activities
        $activities_query = "SELECT DISTINCT a.activity_name 
                             FROM ACTIVITIES a 
                             JOIN ACTIVITIES_PLACES ap ON a.activity_id = ap.activity_id 
                             JOIN PLACES p ON ap.place_id = p.place_id 
                             WHERE p.destination_id = ?";
        $activities_stmt = $conn->prepare($activities_query);
        $activities_stmt->bind_param("i", $destination_id);
        $activities_stmt->execute();
        $activities_result = $activities_stmt->get_result();
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
                    <span class="badge bg-warning text-dark">
                        Rating: <?php echo is_numeric($average_rating) ? "⭐ $average_rating / 5" : $average_rating; ?>
                    </span>
                    <span class="badge bg-info text-dark">
                        ₹<?php echo number_format($row['min_cost']); ?> - ₹<?php echo number_format($row['max_cost']); ?>
                    </span>
                    <hr>
                    <p><strong>Places To Visit:</strong> <?php echo $places_list; ?></p>
                    <p><strong>Activities:</strong> <?php echo $activities_list; ?></p>
                </div>
            </div>
        </div>
        <?php
    }
} else {
    echo "<h5 class='text-center text-muted'>No destinations found.</h5>";
}

// Close connections
$stmt->close();
$conn->close();
?>
