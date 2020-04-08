# Magento 1 Order History Migration to Magento 2

![phpcs](https://github.com/DominicWatts/OrderHistory/workflows/phpcs/badge.svg)

![PHPCompatibility](https://github.com/DominicWatts/OrderHistory/workflows/PHPCompatibility/badge.svg)

![PHPStan](https://github.com/DominicWatts/OrderHistory/workflows/PHPStan/badge.svg)

Easily migrate magento 1 orders to magento 2 and show orders in self contained area in customer account. Offer reorder functionality where possible.

## Install

### Step 1 - On magento 1 store create archive orders tables from sales data

    CREATE TABLE m1_sales_flat_order LIKE sales_flat_order; 
    INSERT INTO m1_sales_flat_order SELECT * FROM sales_flat_order;

    CREATE TABLE m1_sales_flat_order_address LIKE sales_flat_order_address; 
    INSERT INTO m1_sales_flat_order_address SELECT * FROM sales_flat_order_address;

    CREATE TABLE m1_sales_flat_order_item LIKE sales_flat_order_item; 
    INSERT INTO m1_sales_flat_order_item SELECT * FROM sales_flat_order_item;

Included sample sql data in repo (`./supplied/magento.sql`) based on magento 1 sample data

![Screenshot](https://i.snipboard.io/ybitXp.jpg)

### Step 2 - Dump from Magento 1 and import the following tables into your Magento 2 database

    m1_sales_flat_order
    m1_sales_flat_order_address
    m1_sales_flat_order_item

Use `mysqldump` or similar

### Step 3 - Install extension

`composer require dominicwatts/orderhistory`

`php bin/magento setup:upgrade`

`php bin/magento setup:di:compile`

## Usage

    Stores > Configuration > Xigen > Order History

Minor configuration required
  - Map magento 1 order status codes to their labels
    - This is for status label shown on Magento 2 frontend
  - Map Magento 2 Store ID against Magento 1 Store ID
    - This is to ensure correct orders are fetched in multisite scenario

![Screenshot](https://i.snipboard.io/QSYDuo.jpg)

Providing customer email matches logged in customer they will see Magento 1 orders in Previous Order section in their account area with reorder functionality if matching product is found.

My Account > My Previous Orders

![Screenshot](https://i.snipboard.io/F6bYvH.jpg)

## Known issues

  - Will need to convert enum column type to varchar in `m1_*` tables as magento 2 does not support this column type
