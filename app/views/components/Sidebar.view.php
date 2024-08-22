<div class="sidebar">
    <?php

    $sidebarItems = array(
        "/dashboard" => "Dashboard",
        "/products" => "Products",
        "/orders" => "Orders",
        "/discounts" => "Discounts",
        "/suppliers" => "Suppliers",
        "/reports" => "Reports"
    );

    foreach ($sidebarItems as $path => $text) {
        $isSelected = $_SERVER["REDIRECT_URL"] == $path;

        echo "<a ";
        echo !$isSelected ? "href=\"$path\"" : "";
        echo " class=\"sidebar-item";
        echo $isSelected ? " selected" : "";
        echo "\">$text</a>";
    }

    ?>
</div>