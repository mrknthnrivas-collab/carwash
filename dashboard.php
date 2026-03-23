<?php
session_start();
include 'conn.php';


// Prevent page caching (this fixes the back-button issue)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Check if user is logged in
if (!isset($_SESSION['id']) || !isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// ADD product
if (isset($_POST['add'])) {
    $products_name = trim($_POST['products_name']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);

    if (!empty($products_name) && !empty($category) && $price >= 0 && $quantity >= 0) {
        $stmt = $conn->prepare("INSERT INTO products (products_name, category, description, price, quantity) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdi", $products_name, $category, $description, $price, $quantity);
        $stmt->execute();
    } else {
        echo "<script>alert('Please fill in all fields correctly.');</script>";
    }
}

// UPDATE product
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $products_name = trim($_POST['products_name']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);

    if (!empty($products_name) && !empty($category) && $price >= 0 && $quantity >= 0) {
        $stmt = $conn->prepare("UPDATE products SET products_name=?, category=?, description=?, price=?, quantity=? WHERE id=?");
        $stmt->bind_param("sssdiI", $products_name, $category, $description, $price, $quantity, $id);
        $stmt->execute();
    } else {
        echo "<script>alert('Invalid input. Try again.');</script>";
    }
}

// ARCHIVE product
if (isset($_GET['archive'])) {
    $id = $_GET['archive'];
    $conn->query("UPDATE products SET status='archived' WHERE id=$id");
}

// DELETE product
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM products WHERE id=$id");
}

// FETCH active products
$result = $conn->query("SELECT * FROM products WHERE status='active'");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Product Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="tailwind.config.js"></script>
    
</head>

<body>
    <nav class="bg-white dark:bg-gray-800 antialiased">
        <div class="max-w-screen-xl px-4 mx-auto 2xl:px-0 py-4">
            <div class="flex items-center justify-between">

                <div class="flex items-center space-x-8">
                    <div class="shrink-0">
                        <a href="#" title="" class="">
                            <img class="block w-auto h-8 dark:hidden" src="./assets/img/logo.png" alt="">
                            <img class="hidden w-auto h-8 dark:block" src="./assets/img/logo.png" alt="">
                        </a>
                    </div>

                    <ul class="hidden lg:flex items-center justify-start gap-6 md:gap-8 py-3 sm:justify-center">
                        <li>
                            <a href="#" title="" class="flex text-sm font-medium text-gray-900 hover:text-primary-700 dark:text-white dark:hover:text-primary-500">
                                Home
                            </a>
                        </li>
                        <li class="shrink-0">
                            <a href="#" title="" class="flex text-sm font-medium text-gray-900 hover:text-primary-700 dark:text-white dark:hover:text-primary-500">
                                Best Sellers
                            </a>
                        </li>
                        <li class="shrink-0">
                            <a href="#" title="" class="flex text-sm font-medium text-gray-900 hover:text-primary-700 dark:text-white dark:hover:text-primary-500">
                                Gift Ideas
                            </a>
                        </li>
                        <li class="shrink-0">
                            <a href="#" title="" class="text-sm font-medium text-gray-900 hover:text-primary-700 dark:text-white dark:hover:text-primary-500">
                                Today's Deals
                            </a>
                        </li>
                        <li class="shrink-0">
                            <a href="#" title="" class="text-sm font-medium text-gray-900 hover:text-primary-700 dark:text-white dark:hover:text-primary-500">
                                Sell
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="flex items-center lg:space-x-2">

                    <button id="myCartDropdownButton1" data-dropdown-toggle="myCartDropdown1" type="button" class="inline-flex items-center rounded-lg justify-center p-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm font-medium leading-none text-gray-900 dark:text-white">
                        <span class="sr-only">
                            Cart
                        </span>
                        <svg class="w-5 h-5 lg:me-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 4h1.5L9 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8.5-3h9.25L19 7H7.312" />
                        </svg>
                        <span class="hidden sm:flex">My Cart</span>
                        <svg class="hidden sm:flex w-4 h-4 text-gray-900 dark:text-white ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="myCartDropdown1" class="hidden z-10 mx-auto max-w-sm space-y-4 overflow-hidden rounded-lg bg-white p-4 antialiased shadow-lg dark:bg-gray-800">
                        <div class="grid grid-cols-2">
                            <div>
                                <a href="#" class="truncate text-sm font-semibold leading-none text-gray-900 dark:text-white hover:underline">Apple iPhone 15</a>
                                <p class="mt-0.5 truncate text-sm font-normal text-gray-500 dark:text-gray-400">$599</p>
                            </div>

                            <div class="flex items-center justify-end gap-6">
                                <p class="text-sm font-normal leading-none text-gray-500 dark:text-gray-400">Qty: 1</p>

                                <button data-tooltip-target="tooltipRemoveItem1a" type="button" class="text-red-600 hover:text-red-700 dark:text-red-500 dark:hover:text-red-600">
                                    <span class="sr-only"> Remove </span>
                                    <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M2 12a10 10 0 1 1 20 0 10 10 0 0 1-20 0Zm7.7-3.7a1 1 0 0 0-1.4 1.4l2.3 2.3-2.3 2.3a1 1 0 1 0 1.4 1.4l2.3-2.3 2.3 2.3a1 1 0 0 0 1.4-1.4L13.4 12l2.3-2.3a1 1 0 0 0-1.4-1.4L12 10.6 9.7 8.3Z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div id="tooltipRemoveItem1a" role="tooltip" class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-700">
                                    Remove item
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2">
                            <div>
                                <a href="#" class="truncate text-sm font-semibold leading-none text-gray-900 dark:text-white hover:underline">Apple iPad Air</a>
                                <p class="mt-0.5 truncate text-sm font-normal text-gray-500 dark:text-gray-400">$499</p>
                            </div>

                            <div class="flex items-center justify-end gap-6">
                                <p class="text-sm font-normal leading-none text-gray-500 dark:text-gray-400">Qty: 1</p>

                                <button data-tooltip-target="tooltipRemoveItem2a" type="button" class="text-red-600 hover:text-red-700 dark:text-red-500 dark:hover:text-red-600">
                                    <span class="sr-only"> Remove </span>
                                    <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M2 12a10 10 0 1 1 20 0 10 10 0 0 1-20 0Zm7.7-3.7a1 1 0 0 0-1.4 1.4l2.3 2.3-2.3 2.3a1 1 0 1 0 1.4 1.4l2.3-2.3 2.3 2.3a1 1 0 0 0 1.4-1.4L13.4 12l2.3-2.3a1 1 0 0 0-1.4-1.4L12 10.6 9.7 8.3Z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div id="tooltipRemoveItem2a" role="tooltip" class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-700">
                                    Remove item
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2">
                            <div>
                                <a href="#" class="truncate text-sm font-semibold leading-none text-gray-900 dark:text-white hover:underline">Apple Watch SE</a>
                                <p class="mt-0.5 truncate text-sm font-normal text-gray-500 dark:text-gray-400">$598</p>
                            </div>

                            <div class="flex items-center justify-end gap-6">
                                <p class="text-sm font-normal leading-none text-gray-500 dark:text-gray-400">Qty: 2</p>

                                <button data-tooltip-target="tooltipRemoveItem3b" type="button" class="text-red-600 hover:text-red-700 dark:text-red-500 dark:hover:text-red-600">
                                    <span class="sr-only"> Remove </span>
                                    <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M2 12a10 10 0 1 1 20 0 10 10 0 0 1-20 0Zm7.7-3.7a1 1 0 0 0-1.4 1.4l2.3 2.3-2.3 2.3a1 1 0 1 0 1.4 1.4l2.3-2.3 2.3 2.3a1 1 0 0 0 1.4-1.4L13.4 12l2.3-2.3a1 1 0 0 0-1.4-1.4L12 10.6 9.7 8.3Z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div id="tooltipRemoveItem3b" role="tooltip" class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-700">
                                    Remove item
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2">
                            <div>
                                <a href="#" class="truncate text-sm font-semibold leading-none text-gray-900 dark:text-white hover:underline">Sony Playstation 5</a>
                                <p class="mt-0.5 truncate text-sm font-normal text-gray-500 dark:text-gray-400">$799</p>
                            </div>

                            <div class="flex items-center justify-end gap-6">
                                <p class="text-sm font-normal leading-none text-gray-500 dark:text-gray-400">Qty: 1</p>

                                <button data-tooltip-target="tooltipRemoveItem4b" type="button" class="text-red-600 hover:text-red-700 dark:text-red-500 dark:hover:text-red-600">
                                    <span class="sr-only"> Remove </span>
                                    <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M2 12a10 10 0 1 1 20 0 10 10 0 0 1-20 0Zm7.7-3.7a1 1 0 0 0-1.4 1.4l2.3 2.3-2.3 2.3a1 1 0 1 0 1.4 1.4l2.3-2.3 2.3 2.3a1 1 0 0 0 1.4-1.4L13.4 12l2.3-2.3a1 1 0 0 0-1.4-1.4L12 10.6 9.7 8.3Z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div id="tooltipRemoveItem4b" role="tooltip" class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-700">
                                    Remove item
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2">
                            <div>
                                <a href="#" class="truncate text-sm font-semibold leading-none text-gray-900 dark:text-white hover:underline">Apple iMac 20"</a>
                                <p class="mt-0.5 truncate text-sm font-normal text-gray-500 dark:text-gray-400">$8,997</p>
                            </div>

                            <div class="flex items-center justify-end gap-6">
                                <p class="text-sm font-normal leading-none text-gray-500 dark:text-gray-400">Qty: 3</p>

                                <button data-tooltip-target="tooltipRemoveItem5b" type="button" class="text-red-600 hover:text-red-700 dark:text-red-500 dark:hover:text-red-600">
                                    <span class="sr-only"> Remove </span>
                                    <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M2 12a10 10 0 1 1 20 0 10 10 0 0 1-20 0Zm7.7-3.7a1 1 0 0 0-1.4 1.4l2.3 2.3-2.3 2.3a1 1 0 1 0 1.4 1.4l2.3-2.3 2.3 2.3a1 1 0 0 0 1.4-1.4L13.4 12l2.3-2.3a1 1 0 0 0-1.4-1.4L12 10.6 9.7 8.3Z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div id="tooltipRemoveItem5b" role="tooltip" class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-700">
                                    Remove item
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>
                            </div>
                        </div>

                        <a href="#" title="" class="mb-2 me-2 inline-flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800" role="button"> Proceed to Checkout </a>
                    </div>

                    <button id="userDropdownButton1" data-dropdown-toggle="userDropdown1" type="button" class="inline-flex items-center rounded-lg justify-center p-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm font-medium leading-none text-gray-900 dark:text-white">
                        <svg class="w-5 h-5 me-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-width="2" d="M7 17v1a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1a3 3 0 0 0-3-3h-4a3 3 0 0 0-3 3Zm8-9a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        Account
                        <svg class="w-4 h-4 text-gray-900 dark:text-white ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="userDropdown1" class="hidden z-10 w-56 divide-y divide-gray-100 overflow-hidden overflow-y-auto rounded-lg bg-white antialiased shadow dark:divide-gray-600 dark:bg-gray-700">
                        <ul class="p-2 text-start text-sm font-medium text-gray-900 dark:text-white">
                            <li><a href="#" title="" class="inline-flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600"> My Account </a></li>
                            <li><a href="#" title="" class="inline-flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600"> My Orders </a></li>
                            <li><a href="#" title="" class="inline-flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600"> Settings </a></li>
                            <li><a href="#" title="" class="inline-flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600"> Favourites </a></li>
                            <li><a href="#" title="" class="inline-flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600"> Delivery Addresses </a></li>
                            <li><a href="#" title="" class="inline-flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600"> Billing Data </a></li>
                        </ul>

                        <div class="p-2 text-sm font-medium text-gray-900 dark:text-white">
                            <a href="#" title="" class="inline-flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600"> Sign Out </a>
                        </div>
                    </div>

                    <button type="button" data-collapse-toggle="ecommerce-navbar-menu-1" aria-controls="ecommerce-navbar-menu-1" aria-expanded="false" class="inline-flex lg:hidden items-center justify-center hover:bg-gray-100 rounded-md dark:hover:bg-gray-700 p-2 text-gray-900 dark:text-white">
                        <span class="sr-only">
                            Open Menu
                        </span>
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M5 7h14M5 12h14M5 17h14" />
                        </svg>
                    </button>
                </div>
            </div>

            <div id="ecommerce-navbar-menu-1" class="bg-gray-50 dark:bg-gray-700 dark:border-gray-600 border border-gray-200 rounded-lg py-3 hidden px-4 mt-4">
                <ul class="text-gray-900 dark:text-white text-sm font-medium dark:text-white space-y-3">
                    <li>
                        <a href="#" class="hover:text-primary-700 dark:hover:text-primary-500">Home</a>
                    </li>
                    <li>
                        <a href="#" class="hover:text-primary-700 dark:hover:text-primary-500">Best Sellers</a>
                    </li>
                    <li>
                        <a href="#" class="hover:text-primary-700 dark:hover:text-primary-500">Gift Ideas</a>
                    </li>
                    <li>
                        <a href="#" class="hover:text-primary-700 dark:hover:text-primary-500">Games</a>
                    </li>
                    <li>
                        <a href="#" class="hover:text-primary-700 dark:hover:text-primary-500">Electronics</a>
                    </li>
                    <li>
                        <a href="#" class="hover:text-primary-700 dark:hover:text-primary-500">Home & Garden</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto mt-8 p-6 bg-gray-50 rounded-lg shadow">
        <h2 class="text-2xl font-bold mb-3 text-gray-800">🛍️ Product Dashboard</h2>
        <p class="mb-4 text-gray-600">Welcome, <strong class="text-gray-800"><?php echo $_SESSION['username']; ?></strong>!</p>
        <a href="logout.php" class="inline-block bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm mb-6">Logout</a>

        <!-- Add Product Form -->
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <h5 class="text-lg font-semibold mb-3 text-gray-700">Add New Product</h5>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-6 gap-3">
                <input type="text" name="product_name" placeholder="Product Name" class="border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" required>

                <select name="category" class="border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" required>
                    <option value="">Select Category</option>
                    <option value="Shirt">Shirt</option>
                    <option value="Hoodie">Hoodie</option>
                    <option value="Cap">Cap</option>
                    <option value="Sticker">Sticker</option>
                    <option value="Other">Other</option>
                </select>

                <input type="text" name="description" placeholder="Description" class="border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200">
                <input type="number" step="0.01" name="price" placeholder="Price" class="border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" required>
                <input type="number" name="quantity" placeholder="Quantity" class="border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" required>

                <button type="submit" name="add" class="bg-green-500 text-white rounded px-4 py-2 hover:bg-green-600 transition">Add</button>
            </form>
        </div>

        <!-- Product Table -->
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-4 py-2 text-left">ID</th>
                        <th class="px-4 py-2 text-left">Product</th>
                        <th class="px-4 py-2 text-left">Category</th>
                        <th class="px-4 py-2 text-left">Description</th>
                        <th class="px-4 py-2 text-left">Price</th>
                        <th class="px-4 py-2 text-left">Quantity</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-100">
                            <form method="POST" class="contents">
                                <td class="px-4 py-2"><?php echo $row['id']; ?><input type="hidden" name="id" value="<?php echo $row['id']; ?>"></td>
                                <td class="px-4 py-2"><input type="text" name="product_name" value="<?php echo $row['product_name']; ?>" class="border border-gray-300 rounded px-2 py-1 w-full"></td>

                                <td class="px-4 py-2">
                                    <select name="category" class="border border-gray-300 rounded px-2 py-1 w-full">
                                        <?php
                                        $categories = ['Shirt', 'Hoodie', 'Cap', 'Sticker', 'Other'];
                                        foreach ($categories as $cat) {
                                            $selected = ($row['category'] == $cat) ? "selected" : "";
                                            echo "<option value='$cat' $selected>$cat</option>";
                                        }
                                        ?>
                                    </select>
                                </td>

                                <td class="px-4 py-2"><input type="text" name="description" value="<?php echo $row['description']; ?>" class="border border-gray-300 rounded px-2 py-1 w-full"></td>
                                <td class="px-4 py-2"><input type="number" step="0.01" name="price" value="<?php echo $row['price']; ?>" class="border border-gray-300 rounded px-2 py-1 w-full"></td>
                                <td class="px-4 py-2"><input type="number" name="quantity" value="<?php echo $row['quantity']; ?>" class="border border-gray-300 rounded px-2 py-1 w-full"></td>

                                <td class="px-4 py-2 flex flex-wrap gap-2">
                                    <button type="submit" name="edit" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm">Update</button>
                                    <a href="?archive=<?php echo $row['id']; ?>" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 text-sm">Archive</a>
                                    <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this product?')" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm">Delete</a>
                                </td>
                            </form>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.1.1/flowbite.min.js"></script>

</body>

</html>