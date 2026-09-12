<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$base = '';
requireUserLogin(); // must be logged in to book

$showtime_id = intval($_GET['showtime_id'] ?? $_POST['showtime_id'] ?? 0);

$stmt = $conn->prepare("
    SELECT s.showtime_id, s.show_date, s.show_time, s.price, s.theater_id,
           m.movie_id, m.title, m.poster,
           t.name AS theater_name, t.total_seats
    FROM showtimes s
    JOIN movies m ON s.movie_id = m.movie_id
    JOIN theaters t ON s.theater_id = t.theater_id
    WHERE s.showtime_id = ?
");
$stmt->bind_param('i', $showtime_id);
$stmt->execute();
$show = $stmt->get_result()->fetch_assoc();

if (!$show) {
    redirect('index.php');
}

$pageTitle = 'Book Seats - ' . $show['title'];

// Get already booked seats for this showtime
$stmt = $conn->prepare("SELECT seats FROM bookings WHERE showtime_id = ? AND booking_status = 'Confirmed'");
$stmt->bind_param('i', $showtime_id);
$stmt->execute();
$res = $stmt->get_result();
$bookedSeats = [];
while ($row = $res->fetch_assoc()) {
    $bookedSeats = array_merge($bookedSeats, explode(',', $row['seats']));
}

$allSeats = generateSeatMap($show['total_seats']);

$error = '';

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedSeats = isset($_POST['seats']) ? explode(',', $_POST['seats']) : [];
    $selectedSeats = array_filter(array_map('trim', $selectedSeats));

    if (empty($selectedSeats)) {
        $error = 'Please select at least one seat.';
    } else {
        // Re-check none of the selected seats got booked meanwhile
        $conflict = array_intersect($selectedSeats, $bookedSeats);
        if (!empty($conflict)) {
            $error = 'Sorry, seat(s) ' . implode(', ', $conflict) . ' were just booked by someone else. Please choose again.';
        } else {
            $numSeats = count($selectedSeats);
            $total = $numSeats * $show['price'];
            $seatsStr = implode(',', $selectedSeats);
            $user_id = $_SESSION['user_id'];

            $stmt = $conn->prepare("INSERT INTO bookings (user_id, showtime_id, seats, num_seats, total_amount) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('iisid', $user_id, $showtime_id, $seatsStr, $numSeats, $total);
            if ($stmt->execute()) {
                redirect('booking_success.php?id=' . $conn->insert_id);
            } else {
                $error = 'Booking failed. Please try again.';
            }
        }
    }
}

include __DIR__ . '/includes/user_header.php';
?>
<div class="container">
    <h2 class="page-title">Select Your Seats</h2>

    <div class="card" style="background:#fff; padding:20px; border-radius:8px; box-shadow:0 4px 14px rgba(0,0,0,0.12); margin-bottom:24px;">
        <h3><?php echo h($show['title']); ?></h3>
        <p style="color:#666; font-size:14px; margin-top:6px;">
            <?php echo h($show['theater_name']); ?> &middot;
            <?php echo formatDate($show['show_date']); ?> &middot;
            <?php echo formatTime($show['show_time']); ?> &middot;
            <?php echo formatPrice($show['price']); ?> per seat
        </p>
    </div>

    <?php if ($error): ?><div class="alert error"><?php echo h($error); ?></div><?php endif; ?>

    <form method="POST" action="book.php?showtime_id=<?php echo $showtime_id; ?>" id="bookingForm">
        <input type="hidden" name="showtime_id" value="<?php echo $showtime_id; ?>">
        <input type="hidden" name="seats" id="seatsInput" value="">

        <div class="seat-legend">
            <span><span class="legend-box available"></span> Available</span>
            <span><span class="legend-box selected"></span> Selected</span>
            <span><span class="legend-box booked"></span> Booked</span>
        </div>

        <p class="screen-label">S C R E E N &nbsp; T H I S &nbsp; W A Y</p>
        <div class="screen-bar"></div>

        <div class="seat-map">
            <?php
            $rows = [];
            foreach ($allSeats as $seat) {
                $rowLetter = $seat[0];
                $rows[$rowLetter][] = $seat;
            }
            foreach ($rows as $rowLetter => $seatsInRow):
            ?>
                <div class="seat-row">
                    <span class="row-label"><?php echo $rowLetter; ?></span>
                    <?php foreach ($seatsInRow as $seat): ?>
                        <?php $isBooked = in_array($seat, $bookedSeats); ?>
                        <button type="button"
                                class="seat <?php echo $isBooked ? 'booked' : ''; ?>"
                                data-seat="<?php echo $seat; ?>"
                                <?php echo $isBooked ? 'disabled' : ''; ?>
                                onclick="toggleSeat(this)">
                            <?php echo $seat; ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="booking-summary">
            <p>Selected Seats: <strong id="selectedSeatsText">None</strong></p>
            <p class="total" id="totalAmount"><?php echo formatPrice(0); ?></p>
            <button type="submit" class="btn full" id="confirmBtn" disabled>Confirm Booking</button>
        </div>
    </form>
</div>

<script>
const pricePerSeat = <?php echo floatval($show['price']); ?>;
let selected = [];

function toggleSeat(el) {
    const seat = el.dataset.seat;
    if (el.classList.contains('selected')) {
        el.classList.remove('selected');
        selected = selected.filter(s => s !== seat);
    } else {
        el.classList.add('selected');
        selected.push(seat);
    }
    updateSummary();
}

function updateSummary() {
    document.getElementById('selectedSeatsText').innerText = selected.length ? selected.join(', ') : 'None';
    document.getElementById('totalAmount').innerText = '₹' + (selected.length * pricePerSeat).toFixed(2);
    document.getElementById('seatsInput').value = selected.join(',');
    document.getElementById('confirmBtn').disabled = selected.length === 0;
}
</script>

<?php include __DIR__ . '/includes/user_footer.php'; ?>
