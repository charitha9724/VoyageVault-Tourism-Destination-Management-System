<?php
session_start();
require 'new.php'; // Database connection

if (!isset($_SESSION['user_id'])) {
    header("Location: log.php"); // Redirect if not logged in
    exit();
}

if (!isset($_GET['package_id'])) {
    echo "Invalid request!";
    exit();
}

$user_id = $_SESSION['user_id'];
$package_id = intval($_GET['package_id']);

// Package images mapping
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

// Fetch package details
$package_query = "SELECT package_name, cost, duration FROM PACKAGE WHERE package_id = ?";
$stmt = $conn->prepare($package_query);
$stmt->bind_param("i", $package_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Package not found!";
    exit();
}

$package = $result->fetch_assoc();
$package_name = $package['package_name'];
$cost = $package['cost'];
$duration = $package['duration'];

// Assign package image based on package name
$package_image = isset($package_images[$package_name]) ? $package_images[$package_name] : "https://via.placeholder.com/600"; // Default image if not found


// Fetch destinations for this package
$dest_query = "SELECT D.destination_id, D.destination_name, D.state, D.category 
               FROM DESTINATION D
               JOIN PACKAGE_DESTINATION PD ON D.destination_id = PD.destination_id
               WHERE PD.package_id = ?";

$stmt = $conn->prepare($dest_query);
$stmt->bind_param("i", $package_id);
$stmt->execute();
$destinations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Initialize places_by_destination array
$places_by_destination = [];

foreach ($destinations as $dest) {
    $destination_id = $dest['destination_id'];

    // Fetch places for this destination
    $places_query = "SELECT place_id, place_name, place_description FROM PLACES WHERE destination_id = ?";
    $stmt_places = $conn->prepare($places_query);

    if (!$stmt_places) {
        die("Prepare failed for places query: " . $conn->error);
    }

    $stmt_places->bind_param("i", $destination_id);
    $stmt_places->execute();
    $places = $stmt_places->get_result()->fetch_all(MYSQLI_ASSOC);

    // Fetch activities for each place
    foreach ($places as &$place) {
        $place_id = $place['place_id'];
        $activities_query = "SELECT A.activity_name, A.activity_description 
                             FROM ACTIVITIES A 
                             JOIN ACTIVITIES_PLACES AP ON A.activity_id = AP.activity_id 
                             WHERE AP.place_id = ?";

        $stmt_activities = $conn->prepare($activities_query);

        if (!$stmt_activities) {
            die("Prepare failed for activities query: " . $conn->error);
        }

        $stmt_activities->bind_param("i", $place_id);
        $stmt_activities->execute();
        $activities = $stmt_activities->get_result()->fetch_all(MYSQLI_ASSOC);
        $place['activities'] = $activities;
    }

    // Save places by destination ID
    $places_by_destination[$destination_id] = $places;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Package</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2 class="fw-bold text-center"><?= htmlspecialchars($package_name) ?></h2>

    <!-- Package Image -->
    <div class="text-center my-4">
        <img src="<?= htmlspecialchars($package_image) ?>" class="img-fluid" style="max-width: 600px; border-radius: 10px;">
    </div>

    <!-- Package Details -->
    <div class="mt-4">
        <h3>Package Information</h3>
        <p><strong>Cost:</strong> ₹<?= number_format($cost, 2) ?></p>
        <p><strong>Duration:</strong> <?= htmlspecialchars($duration) ?> days</p>
    </div>

    <!-- Destinations with Places & Activities -->
    <div class="mt-4">
        <h3>Destinations Covered</h3>

        <?php foreach ($destinations as $dest) : ?>
            <div class="mb-4">
                <h5><?= htmlspecialchars($dest['destination_name']) ?> (<?= htmlspecialchars($dest['state']) ?>, <?= htmlspecialchars($dest['category']) ?>)</h5>

                <?php
                $destination_id = $dest['destination_id'];
                $places = $places_by_destination[$destination_id] ?? [];
                ?>

                <?php if (!empty($places)) : ?>
                    <ul>
                        <?php foreach ($places as $place) : ?>
                            <li>
                                <strong><?= htmlspecialchars($place['place_name']) ?></strong>: <?= htmlspecialchars($place['place_description']) ?>

                                <?php if (!empty($place['activities'])) : ?>
                                    <ul>
                                        <?php foreach ($place['activities'] as $activity) : ?>
                                            <li><em><?= htmlspecialchars($activity['activity_name']) ?></em>: <?= htmlspecialchars($activity['activity_description']) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else : ?>
                                    <p>No activities listed.</p>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p>No places listed.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Booking Form -->
    <form action="pay.php" method="get" class="mt-4">
        <input type="hidden" name="package_id" value="<?= $package_id ?>">

        <div class="mb-3">
            <label for="start_date" class="form-label"><strong>Select Start Date:</strong></label>
            <input type="date" name="start_date" id="start_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="end_date" class="form-label"><strong>End Date:</strong></label>
            <input type="text" name="end_date" id="end_date" class="form-control" readonly>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-success">Proceed to Payment</button>
        </div>
    </form>

</div>

<script>
document.getElementById('start_date').addEventListener('change', function () {
    const startDate = new Date(this.value);
    const duration = <?= (int)$duration ?>;

    if (!isNaN(startDate.getTime())) {
        const endDate = new Date(startDate);
        endDate.setDate(startDate.getDate() + duration);

        const yyyy = endDate.getFullYear();
        const mm = String(endDate.getMonth() + 1).padStart(2, '0');
        const dd = String(endDate.getDate()).padStart(2, '0');

        document.getElementById('end_date').value = `${yyyy}-${mm}-${dd}`;
    } else {
        document.getElementById('end_date').value = '';
    }
});
</script>

</body>
</html>
