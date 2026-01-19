<?php
require_once 'config.php';

$sql = "CREATE TABLE IF NOT EXISTS `checkout_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(100) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

if (mysqli_query($koneksi, $sql)) {
    echo "Table checkout_item created successfully (or already exists).";
} else {
    echo "Error creating table: " . mysqli_error($koneksi);
}
?>
