<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packages</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Ensure all images are the same size */
        .package-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<?php
include 'new.php';

// Dummy images (Replace with actual images)
$package_images = [
    "Beach Paradise" => "https://live.staticflickr.com/8706/16955089022_059f3d7cfa_b.jpg",
    "Wildlife Explorer" => "https://www.cathaypacific.com/content/dam/focal-point/cx/inspiration/2023/06/What-to-pack-for-a-jungle-adventure-in-Asia-trip-packing-rainforest-Gettyimages-HERO.renditionimage.900.600.jpg",
    "Heritage Tour" => "https://www.india-trip.in/img/category/heritage-orrisa2.jpg",
    "Mountain Adventure" => "https://images.stockcake.com/public/5/5/7/557ca58d-ac58-447d-9085-ee80eb07dbd0_large/mountain-adventure-awaits-stockcake.jpg",
    "Adventure Escapade"=>"https://upload.wikimedia.org/wikipedia/commons/thumb/0/03/Manali_City.jpg/1200px-Manali_City.jpg",
    "Himalayan Thrill"=>"https://static.toiimg.com/thumb/103632064/Bir-Billing.jpg?width=1200&height=900",
    "Cultural Heritage Tour"=>"https://www.maduraitours-travels.com/images/tour-packages/11/ganges-tour.webp",
    "Mughal Trails"=>"https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQdOP8luNr_hNqqjpsSArFMQeFhSTIHW9CBQQ&s",
    "Coastal Serenity"=>"https://hblimg.mmtcdn.com/content/hubble/img/varkala/mmt/activities/t_ufs/m_activities_varkala_varkala_beach_l_403_537.jpg",


];

$sql = "SELECT 
            p.package_id, 
            p.package_name, 
            p.duration, 
            p.cost,
            GROUP_CONCAT(DISTINCT d.destination_name SEPARATOR ', ') AS destinations
        FROM PACKAGE p
        LEFT JOIN PACKAGE_DESTINATION pd ON p.package_id = pd.package_id
        LEFT JOIN DESTINATION d ON pd.destination_id = d.destination_id
        GROUP BY p.package_id";

$result = $conn->query($sql);

echo '<div class="container mt-4">';

// 🌟 **Added Heading**
echo '<h2 class="text-center my-4">Explore Our Travel Packages</h2>';

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $package_name = htmlspecialchars($row['package_name'], ENT_QUOTES, 'UTF-8');
        $image_url = $package_images[$package_name] ?? "https://via.placeholder.com/250x180"; // Default image
        ?>

        <div class="card mb-3 shadow-sm" style="max-width: 900px;">
            <div class="row g-0 align-items-center">
                <div class="col-md-4">
                    <img src="<?php echo $image_url; ?>" class="img-fluid package-img rounded-start" alt="<?php echo $package_name; ?>">
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $package_name; ?></h5>
                        <p class="card-text">
                            <strong>Duration:</strong> <?php echo (int)$row['duration']; ?> days<br>
                            <strong>Cost:</strong> ₹<?php echo number_format((float)$row['cost'], 2); ?><br>
                            <strong>Destinations:</strong> 
                            <?php echo !empty($row['destinations']) ? htmlspecialchars($row['destinations'], ENT_QUOTES, 'UTF-8') : '<span class="text-muted">No destinations assigned</span>'; ?>
                        </p>
                        <a href="book.php?package_id=<?php echo (int)$row['package_id']; ?>" class="btn btn-primary">Book Now</a>
                    </div>
                </div>
            </div>
        </div>

        <?php
    }
} else {
    echo "<h5 class='text-center text-muted'>No packages available.</h5>";
}
echo '</div>';

$conn->close();
?>

<!-- Bootstrap JS (optional, for dropdowns, modals, etc.) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
