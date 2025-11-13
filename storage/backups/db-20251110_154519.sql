-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: chandusoft
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `catalog_enquiries`
--

DROP TABLE IF EXISTS `catalog_enquiries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `catalog_enquiries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `item_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `catalog_enquiries`
--

LOCK TABLES `catalog_enquiries` WRITE;
/*!40000 ALTER TABLE `catalog_enquiries` DISABLE KEYS */;
INSERT INTO `catalog_enquiries` VALUES (1,7,'harish','sivach1982@gmail.com','good product','2025-10-22 13:27:44'),(2,6,'chaitanya','sivacgff@gmail.com','product','2025-10-22 13:50:44'),(3,1,'Harry Potter','sivacgff@gmail.com','good','2025-10-22 15:32:34'),(4,1,'sravani','sravani@gmail.com','red color','2025-10-22 15:38:21'),(5,8,'chaitanyareddy','savich@gmail.com','hello','2025-10-22 16:20:23'),(6,13,'siva','sivach1982@gmail.com','good','2025-10-22 17:48:28'),(7,6,'rakesh','rakesh@gmail.com','hello','2025-10-22 18:07:56'),(8,13,'cherry','cherry@gmail.com','good product','2025-10-23 14:23:11'),(9,26,'sravani','savich@gmail.com','product','2025-10-23 16:12:18'),(10,35,'10% off any order','sivach1982@gmail.com','21525562532','2025-10-29 16:16:36'),(11,41,'sravani','sravani@gmail.com','good product','2025-10-30 09:45:01'),(12,26,'chaitanyareddy','sivach1982@gmail.com','good girl','2025-11-01 08:34:13'),(13,43,'10% off any order','ggj@gmail.com','hghg','2025-11-03 14:24:35');
/*!40000 ALTER TABLE `catalog_enquiries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `catalog_items`
--

DROP TABLE IF EXISTS `catalog_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `catalog_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `short_desc` text COLLATE utf8mb4_unicode_ci,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('published','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_status` (`status`),
  KEY `idx_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `catalog_items`
--

LOCK TABLES `catalog_items` WRITE;
/*!40000 ALTER TABLE `catalog_items` DISABLE KEYS */;
INSERT INTO `catalog_items` VALUES (1,'test','test',50.00,'','uploads/2025/10/ee8ad2903107f9735d85e008a531c44d.png','archived','2025-10-20 11:22:50','2025-10-23 09:40:26'),(4,'test4','test6',50.00,'','uploads/2025/10/f0036b460a619457e98f043e0be0098f.jpg','archived','2025-10-20 11:27:56','2025-10-23 09:40:29'),(5,'4','farm logo',40.00,'farm brand logo','uploads/2025/10/f966772b23309b180fb56acee6a57025.jpg','published','2025-10-20 12:00:57','2025-10-23 09:40:17'),(6,'3','Farm',50.00,'A farm is a piece of land used for growing crops and raising animals for food and other products.','uploads/2025/10/0183066ebe82c696afb480ad51fe314e.jpg','published','2025-10-20 12:08:58','2025-10-23 10:55:22'),(7,'2','farmer with buffalo',50.00,'A farmer with a buffalo is a traditional image of rural life, where the farmer uses the buffalo for plowing fields, milk production, and other agricultural tasks.','uploads/2025/10/eeaf484b0c287b5a5f8edb184a29a6c1.jpg','published','2025-10-20 12:21:26','2025-10-23 10:54:56'),(8,'1','food',58.00,'Food is any substance consumed to provide nutritional support and energy for the body.','uploads/2025/10/414c1a962a45693a40bab68ea5b3f05c.jpg','published','2025-10-20 12:31:10','2025-10-23 10:54:26'),(9,'ram1','ram',40.00,'hello','uploads/2025/10/700b4e9a2526f7b3be8b5450f1fda5c5.png','archived','2025-10-22 10:00:33','2025-10-23 09:35:37'),(11,'products','product',34.00,'red color','uploads/2025/10/47927de029a55c0af910a442f6a3c3fe.png','published','2025-10-22 10:01:38','2025-10-30 04:40:13'),(12,'servicess','testss',54.00,'pink','uploads/2025/10/215b24c144af212427a0b40415cc7274.png','archived','2025-10-22 11:29:21','2025-10-22 12:17:20'),(13,'harry','harry1',40.00,'green','uploads/2025/10/18b6ceb187445fa46775c9287eccfa0a.jpg','archived','2025-10-22 11:44:58','2025-10-23 09:35:32'),(14,'clothing1','clothing',3.00,'white','uploads/2025/10/cc8081a0439b202c2c68ec3757d4ece5.jpg','archived','2025-10-22 11:48:07','2025-10-30 04:21:44'),(15,'strawberry1','strawberry',50.00,'blue color','uploads/2025/10/cf4ec16148e4fb7a1fa1d7bd51ac3ae5.jpg','published','2025-10-23 08:47:53','2025-10-30 04:40:37'),(16,'5','honey',40.00,'Honey is a natural sweetener made by bees from flower nectar, rich in antioxidants and enzymes.','uploads/2025/10/89be06e72eb31e318c3c3f69e4fff6d6.jpg','published','2025-10-23 09:44:54','2025-10-23 10:54:01'),(18,'6','buffalo curd',50.00,'Buffalo curd is a thick, creamy yogurt made from buffalo milk, known for its rich flavor and high fat content.','uploads/2025/10/c54aa43d3db99a83c01a12d0145c28fc.jpg','published','2025-10-23 09:46:58','2025-10-23 10:53:42'),(19,'7','Buffalo Ghee',500.00,'Buffalo ghee is a rich, aromatic clarified butter made from buffalo milk, known for its creamy texture and high nutrition.','uploads/2025/10/8b7c41354d988e854b5e901b079c565f.jpg','published','2025-10-23 09:48:38','2025-10-23 10:53:16'),(20,'9','Milk',56.00,'Milk is a nutritious white liquid produced by mammals, rich in calcium, protein, and vitamins.','uploads/2025/10/ccf4df337d90e0c87a1b534da78cd614.png','published','2025-10-23 09:50:06','2025-10-23 10:52:58'),(21,'10','Nutri',50.00,'Nutri is a nutritious food product made from roasted grains, often rich in protein and fiber.','uploads/2025/10/d30fe7b52d8198928a1aecc4ccffff58.jpg','published','2025-10-23 09:51:45','2025-10-23 10:52:37'),(22,'11','Paneer',200.00,'Paneer is a fresh, soft Indian cheese made by curdling milk with lemon juice or vinegar.','uploads/2025/10/316807a63d1ca8231980e5f8619c88eb.png','published','2025-10-23 09:54:07','2025-10-23 10:52:11'),(23,'13','organic diary products',500.00,'Organic dairy products are milk-based foods made from organically raised animals without synthetic additives or hormones.','uploads/2025/10/c411c7d888caf9b89e3b49c8860f40f9.png','published','2025-10-23 09:57:05','2025-10-23 10:51:45'),(24,'14','Fresh and Thick curd',50.00,'Fresh and thick curd is creamy, smooth yogurt made by fermenting warm milk with live cultures.','uploads/2025/10/eb37133b45e23dcfbd6abb5fb21f5d47.png','published','2025-10-23 10:07:34','2025-10-23 10:51:22'),(25,'15','Fresh Milk',40.00,'Fresh milk is a nutrient-rich liquid directly obtained from cows, buffaloes.','uploads/2025/10/d4faf6a299ce21259f9bd33d73c5e966.jpg','published','2025-10-23 10:10:53','2025-10-23 10:50:57'),(26,'16','Paneer',600.00,'Paneer is a soft, fresh Indian cheese made by curdling milk with lemon juice or vinegar.','uploads/2025/10/6f47aca85782c7d08cf74e54b906748e.jpg','published','2025-10-23 10:16:47','2025-10-23 10:49:49'),(27,'our-services','harry',56.00,'','uploads/2025/10/65588458d7442f250cb5c05f17180437.jpg','archived','2025-10-27 04:06:38','2025-10-30 04:21:17'),(28,'test2','harry1',56.00,'','uploads/2025/10/9a79738c73d702c2a5b2cc30a694bd44.jpg','archived','2025-10-27 05:03:19','2025-10-30 04:21:06'),(29,'blue','hat',66.00,'red color','uploads/2025/10/665999ec9eb5239f78164d9043ced9aa.png','archived','2025-10-27 05:16:17','2025-10-30 04:20:53'),(31,'test7','hat',78.00,'hello','','archived','2025-10-27 05:20:11','2025-10-30 04:18:47'),(32,'23','title',45.00,'test 23','uploads/2025/10/d510d2499f7f293846ec1fdb73069f1f.jpg','archived','2025-10-27 05:46:46','2025-10-30 04:20:06'),(33,'white','tables',34.00,'table top','uploads/2025/10/f50f84f2583b07aa1e4b724708caf3f8.jpg','archived','2025-10-27 06:19:20','2025-10-30 04:18:52'),(35,'26','harry1',8787.00,'hello','uploads/2025/10/bc687703d4237f9b3924a8e0414b980c.jpg','archived','2025-10-27 06:54:28','2025-10-30 04:19:44'),(36,'55','harry1',56.00,'good','uploads/2025/10/5df67208e09e6886796d3cf85ba8e2f3.jpg','archived','2025-10-27 06:56:09','2025-10-27 07:01:54'),(37,'34','food',456.00,'tag','uploads/2025/10/3cf953cab536bfa9ad94a8da734a764a.jpg','archived','2025-10-27 07:01:04','2025-10-27 07:01:36'),(39,'56','violin',456.00,'red colour','uploads/2025/10/d3d9aab43f5c03a2a610a2144fd76f72.png','archived','2025-10-27 07:03:28','2025-10-30 04:18:37'),(40,'lockets','lockets',46.00,'','uploads/2025/10/57060aaaa43925bfb2fce0068f9ee669.jpg','archived','2025-10-29 09:37:08','2025-10-30 04:18:33'),(41,'slugs','slugs',78.00,'','uploads/2025/10/aa4c951e0eca7198cd3ea5fa06c1c5c9.png','archived','2025-10-29 09:48:35','2025-10-30 04:18:25'),(42,'product','Products',48.00,'good one','uploads/2025/10/b9cf3526af6cbe99b23eac818de4d2fd.jpg','archived','2025-10-30 04:08:14','2025-10-30 04:19:39'),(43,'46','phone',568.00,'new versions','uploads/2025/11/ca324acadd8cb0c82e407524af586d59.jpg','archived','2025-11-03 08:52:45','2025-11-03 09:47:13');
/*!40000 ALTER TABLE `catalog_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leads`
--

DROP TABLE IF EXISTS `leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leads` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leads`
--

LOCK TABLES `leads` WRITE;
/*!40000 ALTER TABLE `leads` DISABLE KEYS */;
INSERT INTO `leads` VALUES (1,'Alice Smith','alice1@example.com','Hello, this is Alice 1.','2025-10-06 08:29:28',NULL),(2,'Bob Johnson','bob2@example.com','Message from Bob 2.','2025-10-06 08:29:28',NULL),(3,'Charlie Lee','charlie3@example.com','Hello from Charlie 3.','2025-10-06 08:29:28',NULL),(4,'David Brown','david4@example.com','Hi, David here 4.','2025-10-06 08:29:28',NULL),(5,'Eva Green','eva5@example.com','Eva says hello 5.','2025-10-06 08:29:28',NULL),(6,'Frank White','frank6@example.com','Message from Frank 6.','2025-10-06 08:29:28',NULL),(7,'Grace Black','grace7@example.com','Grace here, message 7.','2025-10-06 08:29:28',NULL),(8,'Hannah Scott','hannah8@example.com','Hello from Hannah 8.','2025-10-06 08:29:28',NULL),(9,'Ian Clarke','ian9@example.com','Ian\'s message 9.','2025-10-06 08:29:28',NULL),(10,'Julia Adams','julia10@example.com','Message from Julia 10.','2025-10-06 08:29:28',NULL),(11,'Kevin Young','kevin11@example.com','Hi, Kevin here 11.','2025-10-06 08:29:28',NULL),(12,'Lily King','lily12@example.com','Lily\'s message 12.','2025-10-06 08:29:28',NULL),(13,'Mark Hill','mark13@example.com','Hello from Mark 13.','2025-10-06 08:29:28',NULL),(14,'Nina Wright','nina14@example.com','Message from Nina 14.','2025-10-06 08:29:28',NULL),(15,'Oscar Turner','oscar15@example.com','Oscar says hi 15.','2025-10-06 08:29:28',NULL),(16,'Paula Martin','paula16@example.com','Message from Paula 16.','2025-10-06 08:29:28',NULL),(17,'Quinn Walker','quinn17@example.com','Quinn\'s message 17.','2025-10-06 08:29:28',NULL),(18,'Rachel Allen','rachel18@example.com','Hello from Rachel 18.','2025-10-06 08:29:28',NULL),(19,'Steve Harris','steve19@example.com','Message from Steve 19.','2025-10-06 08:29:28',NULL),(20,'Tina Nelson','tina20@example.com','Tina says hello 20.','2025-10-06 08:29:28',NULL),(21,'kavitha','kavitha@gmail.com','hello','2025-10-06 09:08:24','127.0.0.1'),(22,'ramya','ramya@gmail.com','hello','2025-10-07 06:54:23','127.0.0.1'),(23,'rahul','rahul@gmail.com','hi...','2025-10-07 10:49:32','127.0.0.1'),(24,'charan','charan@gmail.com','hello....','2025-10-07 11:30:32','127.0.0.1'),(25,'harish','harish@gmail.com','happy to see you...','2025-10-07 11:33:36','127.0.0.1'),(26,'chaitanyareddy','chaitanya@gmail.com','happy....','2025-10-07 11:46:09','127.0.0.1'),(27,'raju','raju@gmail.com','hello...','2025-10-07 12:52:02','127.0.0.1'),(28,'haris','haris@gmail.com','hello','2025-10-07 12:54:21','127.0.0.1'),(29,'hi','sivach1982@gmail.com','hello','2025-10-08 04:05:42','127.0.0.1'),(30,'rakesh','rakesh@gmail.com','good...','2025-10-08 04:17:37','127.0.0.1'),(31,'rakesh','rakesh@gmail.com','good...','2025-10-08 04:20:05','127.0.0.1'),(32,'rakesh','rakesh@gmail.com','good...','2025-10-08 04:24:44','127.0.0.1'),(33,'rama','rama@gmail.com','hello','2025-10-08 04:36:46','127.0.0.1'),(34,'chaitanyareddya','chaitanya56@gmail.com','hi','2025-10-08 04:48:15','127.0.0.1'),(35,'maneesh','maneesh@gmail.com','hello....','2025-10-08 04:51:19','127.0.0.1'),(36,'maneesh','maneesh@gmail.com','hello...','2025-10-08 04:52:46','127.0.0.1'),(37,'siva','sivach1982@gmail.com','hi','2025-10-08 12:13:28','127.0.0.1'),(38,'harry','harry@gmail.com','hello...','2025-10-08 12:40:38','127.0.0.1'),(39,'chaitanyaa','chaitanyaa@gmail.com','Hi...','2025-10-09 11:39:10','127.0.0.1'),(40,'sneha','sneha@gmail.com','hi;;;','2025-10-10 09:23:01','127.0.0.1'),(41,'haris','haris@gmail.com','good','2025-10-24 08:19:41','127.0.0.1'),(42,'sivani','sivani@gmail.com','hello','2025-10-27 11:16:24','127.0.0.1'),(43,'shivas','shivas@gmail.com','hello','2025-10-28 07:10:37','127.0.0.1'),(44,'hari narayana','harinarayana@gmail.com','hi','2025-10-28 08:50:43','127.0.0.1'),(45,'sameer','sameer@gmail.com','hi...','2025-10-29 11:22:41','127.0.0.1'),(46,'harrys','harrys@gmail.com','work work','2025-11-03 08:23:09','127.0.0.1');
/*!40000 ALTER TABLE `leads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=298 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (284,218,26,3,600.00),(285,219,26,1,600.00),(286,220,25,1,40.00),(287,221,25,1,40.00),(288,222,26,1,600.00),(289,223,23,1,500.00),(290,224,23,1,500.00),(291,225,26,1,600.00),(292,226,26,1,600.00),(293,227,24,1,50.00),(294,228,26,1,600.00),(295,229,25,1,40.00),(296,230,25,1,40.00),(297,231,24,1,50.00);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_address` text,
  `total_amount` decimal(10,2) NOT NULL,
  `txn_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `status` enum('pending','paid','fulfilled','cancelled','failed') NOT NULL DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `payment_gateway` varchar(50) NOT NULL,
  `order_ref` varchar(100) NOT NULL,
  `stripe_session_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=232 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (218,'harish','harish@gmail.com','17 West Bayview Avenue',1800.00,'pi_3SRm95ALkNea1eRv3TQmgCmu','paid','2025-11-10 09:21:02','stripe','ORD-69116126A16D5',NULL),(219,'hari','harish@gmail.com','17 West Bayview Avenue',600.00,NULL,'failed','2025-11-10 09:30:18','stripe','ORD-69116352CB63E',NULL),(220,'chaitanya','chaitanya@gmail.com','17 West Bayview Avenue',40.00,'pi_3SRmTeALkNea1eRv3rVxahvV','paid','2025-11-10 09:42:49','stripe','ORD-69116641423CE',NULL),(221,'keerthi','keerthi@gmail.com','17 West Bayview Avenue',40.00,NULL,'pending','2025-11-10 10:01:55','stripe','ORD-69116ABB0F994',NULL),(222,'nanditha','nanditha@gmail.com','hyderabad',600.00,'pi_3SRqccALkNea1eRv0iTiy4hK','paid','2025-11-10 14:08:23','stripe','ORD-6911A47F97C39',NULL),(223,'siva','sivach1982@gmail.com','17 West Bayview Avenue',500.00,NULL,'failed','2025-11-10 14:14:53','stripe','ORD-6911A605DDBCD',NULL),(224,'sunitha','sunitha@gmail.com','pottipadu',500.00,'pi_3SRqtbALkNea1eRv2kSZKDVx','paid','2025-11-10 14:25:43','stripe','ORD-6911A88F66193',NULL),(225,'sravani','sravani@gmail.com','17 West Bayview Avenue',600.00,'pi_3SRqxHALkNea1eRv2y4zvlNr','paid','2025-11-10 14:28:21','stripe','ORD-6911A92D90D28',NULL),(226,'sunila','sunila@gmail.com','26 Kings Road',600.00,'pi_3SRr81ALkNea1eRv1yw5bQu3','paid','2025-11-10 14:38:53','stripe','ORD-6911ABA5F21E9',NULL),(227,'siva','sivach1982@gmail.com','17 West Bayview Avenue',50.00,'pi_3SRrnEALkNea1eRv24vuXrPk','paid','2025-11-10 14:47:20','stripe','ORD-6911ADA0EDFA0','cs_test_a1GjxHBpuHJ4XbM7LQw9r3ZKK4i9f91LSSIFeGCu9FOPeKZz7Zi1EXuXDL'),(228,'bala','bala@gmail.com','17 West Bayview Avenue',600.00,'pi_3SRrq9ALkNea1eRv3cprY54L','paid','2025-11-10 15:26:22','stripe','ORD-6911B6C619591','cs_test_a1fYR99LSejvH6o01TymiPUTzoeuBG9kx2TsvMyc3yT1dtGlRyG6Gd68pd'),(229,'girish','girish@gmail.com','telaprolu',40.00,NULL,'failed','2025-11-10 15:28:57','stripe','ORD-6911B7615F646','cs_test_a1cBFwYCQTZUMt93XbsW3jVhTtdtbbpqSsskubF79AGo5f4nzJfGoth0A1'),(230,'musthafa','musthafa@gmail.com','gannavaram',40.00,NULL,'failed','2025-11-10 15:33:16','stripe','ORD-6911B8644B41A','cs_test_a1mVq3AKQiNbWyTqKG9dT8xvCjYGTErxpF9Dnr3UoGRvioeCdExTDnh15d'),(231,'sravanii','sravanii@gmail.com','17 West Bayview Avenue',50.00,NULL,'pending','2025-11-10 15:36:13','stripe','ORD-6911B915DDA04','cs_test_a1O92iRYS7wtrHEXQUWFnNgS5LQxsnM0xPe0pfUKo5NQmVJNUbo85UOGvQ');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `status` enum('published','draft','archived') DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `content_html` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `meta_json` json DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES (2,'Contact Us','contact-us','draft','2025-10-03 16:01:26',NULL,NULL),(3,'About Us','about-us','draft','2025-10-09 11:28:43','<p>This is our FAQ section.</p>',NULL),(4,'Our Services1','our-services','draft','2025-10-07 12:45:12',NULL,NULL),(5,'test-services','test-services','draft','2025-10-07 04:29:15',NULL,NULL),(6,'our services 2','our services 2','draft','2025-10-07 07:12:10',NULL,NULL),(7,'our services 3','our services 3','draft','2025-10-08 16:31:49',NULL,NULL),(8,'about','about','archived','2025-10-27 16:51:32','hello',NULL),(9,'hello','hello','draft','2025-10-08 18:18:09','hi',NULL),(10,'about2','about2','draft','2025-10-08 16:44:10','<!DOCTYPE html>\r\n<html lang=\"en\">\r\n<head>\r\n  <meta charset=\"UTF-8\" />\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />\r\n  <title>About Chandusoft</title>\r\n  <link rel=\"stylesheet\" href=\"styles.css\" />\r\n  <link\r\n    rel=\"stylesheet\"\r\n    href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css\"\r\n  />\r\n</head>\r\n<body>\r\n  <!-- Header placeholder -->\r\n  <div id=\"header\"></div>\r\n  <?php include(\"header.php\"); ?>\r\n\r\n  <main>\r\n    <h2>About Us</h2>\r\n    <section>\r\n      <p>\r\n        <span class=\"highlight\">Chandusoft</span> is a well-established company with over\r\n        <span class=\"highlight\">15 years</span> of experience in delivering IT and BPO solutions.\r\n        We have a team of more than <span class=\"highlight\">200 skilled professionals</span> operating\r\n        from multiple locations. One of our key strengths is <span class=\"highlight\">24/7 operations</span>,\r\n        which allows us to support clients across different time zones. We place a strong emphasis on\r\n        <span class=\"highlight\">data integrity</span> and <span class=\"highlight\">security</span>, which has helped\r\n        us earn long-term trust from our partners. Our core service areas include\r\n        <span class=\"highlight\">Software Development</span>, <span class=\"highlight\">Medical Process Services</span>,\r\n        and <span class=\"highlight\">E-Commerce Solutions</span>, all backed by a commitment to quality and process excellence.\r\n      </p>\r\n    </section>\r\n  </main>\r\n\r\n  <!-- Footer placeholder -->\r\n  <div id=\"footer\"></div>\r\n  <?php include(\"footer.php\"); ?>\r\n\r\n</body>\r\n</html>',NULL),(11,'about3','about3','draft','2025-10-08 16:55:25','<br />\r\n<b>Warning</b>:  Undefined variable $content_html in <b>C:\\laragon\\www\\chandusoft\\create.php</b> on line <b>196</b><br />\r\n<br />\r\n<b>Deprecated</b>:  htmlspecialchars(): Passing null to parameter #1 ($string) of type string is deprecated in <b>C:\\laragon\\www\\chandusoft\\create.php</b> on line <b>196</b><br />',NULL),(12,'history','history','draft','2025-10-08 12:15:32','',NULL),(13,'history','history','draft','2025-10-08 12:15:59','',NULL),(14,'hello','hello','draft','2025-10-08 12:41:46',NULL,NULL),(15,'services','services','draft','2025-10-09 09:06:18','',NULL),(16,'home','home','draft','2025-10-09 13:05:53','hi',NULL),(17,'About','About','draft','2025-10-10 11:15:59','<!DOCTYPE html>\r\n<html lang=\"en\">\r\n<head>\r\n  <meta charset=\"UTF-8\" />\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />\r\n  <title>About Chandusoft</title>\r\n  <link rel=\"stylesheet\" href=\"styles.css\" />\r\n  <link\r\n    rel=\"stylesheet\"\r\n    href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css\"\r\n  />\r\n</head>\r\n<body>\r\n\r\n\r\n  <main>\r\n    <h2>About Us</h2>\r\n    <section>\r\n      <p>\r\n        <span class=\"highlight\">Chandusoft</span> is a well-established company with over\r\n        <span class=\"highlight\">15 years</span> of experience in delivering IT and BPO solutions.\r\n        We have a team of more than <span class=\"highlight\">200 skilled professionals</span> operating\r\n        from multiple locations. One of our key strengths is <span class=\"highlight\">24/7 operations</span>,\r\n        which allows us to support clients across different time zones. We place a strong emphasis on\r\n        <span class=\"highlight\">data integrity</span> and <span class=\"highlight\">security</span>, which has helped\r\n        us earn long-term trust from our partners. Our core service areas include\r\n        <span class=\"highlight\">Software Development</span>, <span class=\"highlight\">Medical Process Services</span>,\r\n        and <span class=\"highlight\">E-Commerce Solutions</span>, all backed by a commitment to quality and process excellence.\r\n      </p>\r\n    </section>\r\n  </main>\r\n\r\n\r\n</body>\r\n</html>\r\n',NULL),(18,'services','services','archived','2025-10-09 14:48:06','<!DOCTYPE html>\r\n<html lang=\"en\">\r\n<head>\r\n  <meta charset=\"UTF-8\" />\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />\r\n  <title>About Chandusoft</title>\r\n  <link rel=\"stylesheet\" href=\"styles.css\" />\r\n  <link\r\n    rel=\"stylesheet\"\r\n    href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css\"\r\n  />\r\n</head>\r\n<body>\r\n  <!-- Header placeholder -->\r\n  <div id=\"header\"></div>\r\n  <?php include(\"header.php\"); ?>\r\n\r\n  <main>\r\n    <h2>About Us</h2>\r\n    <section>\r\n      <p>\r\n        <span class=\"highlight\">Chandusoft</span> is a well-established company with over\r\n        <span class=\"highlight\">15 years</span> of experience in delivering IT and BPO solutions.\r\n        We have a team of more than <span class=\"highlight\">200 skilled professionals</span> operating\r\n        from multiple locations. One of our key strengths is <span class=\"highlight\">24/7 operations</span>,\r\n        which allows us to support clients across different time zones. We place a strong emphasis on\r\n        <span class=\"highlight\">data integrity</span> and <span class=\"highlight\">security</span>, which has helped\r\n        us earn long-term trust from our partners. Our core service areas include\r\n        <span class=\"highlight\">Software Development</span>, <span class=\"highlight\">Medical Process Services</span>,\r\n        and <span class=\"highlight\">E-Commerce Solutions</span>, all backed by a commitment to quality and process excellence.\r\n      </p>\r\n    </section>\r\n  </main>\r\n\r\n  <!-- Footer placeholder -->\r\n  <div id=\"footer\"></div>\r\n  <?php include(\"footer.php\"); ?>\r\n\r\n</body>\r\n</html>\r\n',NULL),(19,'history','history','draft','2025-10-09 13:45:42','',NULL),(20,'About Us','about-us','archived','2025-10-10 11:52:36','<!DOCTYPE html>\r\n<html lang=\"en\">\r\n<head>\r\n  <meta charset=\"UTF-8\" />\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />\r\n  <link rel=\"stylesheet\" href=\"styles.css\" />\r\n  <link\r\n    rel=\"stylesheet\"\r\n    href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css\"\r\n  />\r\n</head>\r\n<body>\r\n\r\n  <main>\r\n    <section>\r\n      <p>\r\n        <span class=\"highlight\">Chandusoft</span> is a well-established company with over\r\n        <span class=\"highlight\">15 years</span> of experience in delivering IT and BPO solutions.\r\n        We have a team of more than <span class=\"highlight\">200 skilled professionals</span> operating\r\n        from multiple locations. One of our key strengths is <span class=\"highlight\">24/7 operations</span>,\r\n        which allows us to support clients across different time zones. We place a strong emphasis on\r\n        <span class=\"highlight\">data integrity</span> and <span class=\"highlight\">security</span>, which has helped\r\n        us earn long-term trust from our partners. Our core service areas include\r\n        <span class=\"highlight\">Software Development</span>, <span class=\"highlight\">Medical Process Services</span>,\r\n        and <span class=\"highlight\">E-Commerce Solutions</span>, all backed by a commitment to quality and process excellence\r\n      </p>\r\n    </section>\r\n  </main>\r\n\r\n  \r\n</body>\r\n</html>\r\n',NULL),(21,'Services','services','archived','2025-10-10 15:37:34','<!DOCTYPE html>\r\n<html lang=\"en\">\r\n<head>\r\n  <meta charset=\"UTF-8\" />\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />\r\n  <title>Chandusoft - Services</title>\r\n  <link rel=\"stylesheet\" href=\"styles.css\" />\r\n  <link\r\n    rel=\"stylesheet\"\r\n    href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css\"\r\n  />\r\n</head>\r\n<body>\r\n\r\n  <!-- Header will be dynamically loaded here -->\r\n  <div id=\"header\"></div>\r\n  <?php include(\"header.php\"); ?>\r\n\r\n  <main>\r\n    <section id=\"Services\">\r\n      <h2>Our Services</h2>\r\n      <div class=\"services-container\">\r\n\r\n        <div class=\"service-card\">\r\n          <i class=\"fas fa-building icon-blue\"></i>\r\n          <h3>Enterprise Application Solution</h3>\r\n          <p>Robust enterprise apps for seamless business operations.</p>\r\n        </div>\r\n\r\n        <div class=\"service-card\">\r\n          <i class=\"fas fa-mobile-alt icon-green\"></i>\r\n          <h3>Mobile Application Solution</h3>\r\n          <p>Cross-platform mobile apps with modern UI/UX.</p>\r\n        </div>\r\n\r\n        <div class=\"service-card\">\r\n          <i class=\"fas fa-laptop icon-black\"></i>\r\n          <h3>Web Portal Design & Solution</h3>\r\n          <p>Custom web portals for business and customer engagement.</p>\r\n        </div>\r\n\r\n        <div class=\"service-card\">\r\n          <i class=\"fas fa-tools icon-yellow\"></i>\r\n          <h3>Web Portal Maintenance & Content Management</h3>\r\n          <p>Continuous support, updates, and content handling.</p>\r\n        </div>\r\n\r\n        <div class=\"service-card\">\r\n          <i class=\"fas fa-vial icon-purple\"></i>\r\n          <h3>QA & Testing</h3>\r\n          <p>Quality assurance and testing for bug-free releases.</p>\r\n        </div>\r\n\r\n        <div class=\"service-card\">\r\n          <i class=\"fas fa-phone icon-red\"></i>\r\n          <h3>Business Process Outsourcing</h3>\r\n          <p>End-to-end BPO services with 24/7 operations.</p>\r\n        </div>\r\n\r\n      </div>\r\n    </section>\r\n  </main>\r\n\r\n  <!-- Footer will be dynamically loaded here -->\r\n  <div id=\"footer\"></div>\r\n  <?php include(\"footer.php\"); ?>\r\n\r\n  \r\n\r\n</body>\r\n</html>\r\n',NULL),(22,'history part','history-part','archived','2025-10-10 17:13:01','',NULL),(23,'services5','36','archived','2025-10-27 17:19:57','',NULL),(24,'About','about','archived','2025-10-28 09:39:52','<!DOCTYPE html>\r\n<html lang=\"en\">\r\n<head>\r\n  <meta charset=\"UTF-8\" />\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />\r\n  <link rel=\"stylesheet\" href=\"styles.css\" />\r\n  <link\r\n    rel=\"stylesheet\"\r\n    href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css\"\r\n  />\r\n</head>\r\n<body>\r\n\r\n  <main>\r\n    <section>\r\n      <p>\r\n        <span class=\"highlight\">Chandusoft</span> is a well-established company with over\r\n        <span class=\"highlight\">15 years</span> of experience in delivering IT and BPO solutions.\r\n        We have a team of more than <span class=\"highlight\">200 skilled professionals</span> operating\r\n        from multiple locations. One of our key strengths is <span class=\"highlight\">24/7 operations</span>,\r\n        which allows us to support clients across different time zones. We place a strong emphasis on\r\n        <span class=\"highlight\">data integrity</span> and <span class=\"highlight\">security</span>, which has helped\r\n        us earn long-term trust from our partners. Our core service areas include\r\n        <span class=\"highlight\">Software Development</span>, <span class=\"highlight\">Medical Process Services</span>,\r\n        and <span class=\"highlight\">E-Commerce Solutions</span>, all backed by a commitment to quality and process excellence\r\n      </p>\r\n    </section>\r\n  </main>\r\n\r\n  \r\n</body>\r\n</html>',NULL),(25,'about','about','archived','2025-10-28 11:59:42','hello',NULL),(26,'gett','bo','archived','2025-10-28 09:10:12','',NULL),(27,'geti','getin','archived','2025-10-28 08:59:01','',NULL),(28,'users','users','archived','2025-10-28 03:35:54','',NULL),(29,'users','users','archived','2025-10-28 03:36:23','',NULL),(30,'getting','getting','archived','2025-10-28 03:38:51','',NULL),(31,'gets','gets','archived','2025-10-28 03:40:47','',NULL),(32,'get','get','archived','2025-10-28 03:41:48','',NULL),(33,'page1','page1','archived','2025-10-28 03:44:51','',NULL),(34,'page2','page2','archived','2025-10-28 03:45:25','',NULL),(35,'page3','page3','archived','2025-10-28 03:47:00','',NULL),(36,'create','create','archived','2025-10-28 04:03:36','',NULL),(37,'services','services','archived','2025-10-28 12:01:26','hello',NULL),(38,'About1','about','archived','2025-11-03 14:05:20','<!DOCTYPE html>\r\n<html lang=\"en\">\r\n<head>\r\n  <meta charset=\"UTF-8\" />\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />\r\n  <link rel=\"stylesheet\" href=\"styles.css\" />\r\n  <link\r\n    rel=\"stylesheet\"\r\n    href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css\"\r\n  />\r\n</head>\r\n<body>\r\n\r\n  <main>\r\n    <section>\r\n      <p>\r\n        <span class=\"highlight\">Chandusoft</span> is a well-established company with over\r\n        <span class=\"highlight\">15 years</span> of experience in delivering IT and BPO solutions.\r\n        We have a team of more than <span class=\"highlight\">200 skilled professionals</span> operating\r\n        from multiple locations. One of our key strengths is <span class=\"highlight\">24/7 operations</span>,\r\n        which allows us to support clients across different time zones. We place a strong emphasis on\r\n        <span class=\"highlight\">data integrity</span> and <span class=\"highlight\">security</span>, which has helped\r\n        us earn long-term trust from our partners. Our core service areas include\r\n        <span class=\"highlight\">Software Development</span>, <span class=\"highlight\">Medical Process Services</span>,\r\n        and <span class=\"highlight\">E-Commerce Solutions</span>, all backed by a commitment to quality and process excellence\r\n      </p>\r\n    </section>\r\n  </main>\r\n\r\n  \r\n</body>\r\n</html>',NULL),(39,'services1','services','archived','2025-10-29 08:59:50','<!DOCTYPE html>\r\n<html lang=\"en\">\r\n<head>\r\n  <meta charset=\"UTF-8\" />\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />\r\n  <title>Chandusoft - Services</title>\r\n  <link rel=\"stylesheet\" href=\"styles.css\" />\r\n  <link\r\n    rel=\"stylesheet\"\r\n    href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css\"\r\n  />\r\n</head>\r\n<body>\r\n\r\n  <!-- Header will be dynamically loaded here -->\r\n  <div id=\"header\"></div>\r\n  <?php include(\"header.php\"); ?>\r\n\r\n  <main>\r\n    <section id=\"Services\">\r\n      <h2>Our Services</h2>\r\n      <div class=\"services-container\">\r\n\r\n        <div class=\"service-card\">\r\n          <i class=\"fas fa-building icon-blue\"></i>\r\n          <h3>Enterprise Application Solution</h3>\r\n          <p>Robust enterprise apps for seamless business operations.</p>\r\n        </div>\r\n\r\n        <div class=\"service-card\">\r\n          <i class=\"fas fa-mobile-alt icon-green\"></i>\r\n          <h3>Mobile Application Solution</h3>\r\n          <p>Cross-platform mobile apps with modern UI/UX.</p>\r\n        </div>\r\n\r\n        <div class=\"service-card\">\r\n          <i class=\"fas fa-laptop icon-black\"></i>\r\n          <h3>Web Portal Design & Solution</h3>\r\n          <p>Custom web portals for business and customer engagement.</p>\r\n        </div>\r\n\r\n        <div class=\"service-card\">\r\n          <i class=\"fas fa-tools icon-yellow\"></i>\r\n          <h3>Web Portal Maintenance & Content Management</h3>\r\n          <p>Continuous support, updates, and content handling.</p>\r\n        </div>\r\n\r\n        <div class=\"service-card\">\r\n          <i class=\"fas fa-vial icon-purple\"></i>\r\n          <h3>QA & Testing</h3>\r\n          <p>Quality assurance and testing for bug-free releases.</p>\r\n        </div>\r\n\r\n        <div class=\"service-card\">\r\n          <i class=\"fas fa-phone icon-red\"></i>\r\n          <h3>Business Process Outsourcing</h3>\r\n          <p>End-to-end BPO services with 24/7 operations.</p>\r\n        </div>\r\n\r\n      </div>\r\n    </section>\r\n  </main>\r\n\r\n  <!-- Footer will be dynamically loaded here -->\r\n  <div id=\"footer\"></div>\r\n  <?php include(\"footer.php\"); ?>\r\n\r\n  \r\n\r\n</body>\r\n</html>\r\n',NULL),(40,'Servicess','Services1','draft','2025-11-03 13:58:57','<!DOCTYPE html>\r\n<html lang=\"en\">\r\n<head>\r\n  <meta charset=\"UTF-8\" />\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />\r\n  <title>Chandusoft - Services</title>\r\n  <link rel=\"stylesheet\" href=\"styles.css\" />\r\n  <link\r\n    rel=\"stylesheet\"\r\n    href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css\"\r\n  />\r\n</head>\r\n<body>\r\n\r\n  <!-- Header will be dynamically loaded here -->\r\n  <div id=\"header\"></div>\r\n  <?php include(\"header.php\"); ?>\r\n\r\n  <main>\r\n    <section id=\"Services\">\r\n      <h2>Our Services</h2>\r\n      <div class=\"services-container\">\r\n\r\n        <div class=\"service-card\">\r\n          <i class=\"fas fa-building icon-blue\"></i>\r\n          <h3>Enterprise Application Solution</h3>\r\n          <p>Robust enterprise apps for seamless business operations.</p>\r\n        </div>\r\n\r\n        <div class=\"service-card\">\r\n          <i class=\"fas fa-mobile-alt icon-green\"></i>\r\n          <h3>Mobile Application Solution</h3>\r\n          <p>Cross-platform mobile apps with modern UI/UX.</p>\r\n        </div>\r\n\r\n        <div class=\"service-card\">\r\n          <i class=\"fas fa-laptop icon-black\"></i>\r\n          <h3>Web Portal Design & Solution</h3>\r\n          <p>Custom web portals for business and customer engagement.</p>\r\n        </div>\r\n\r\n        <div class=\"service-card\">\r\n          <i class=\"fas fa-tools icon-yellow\"></i>\r\n          <h3>Web Portal Maintenance & Content Management</h3>\r\n          <p>Continuous support, updates, and content handling.</p>\r\n        </div>\r\n\r\n        <div class=\"service-card\">\r\n          <i class=\"fas fa-vial icon-purple\"></i>\r\n          <h3>QA & Testing</h3>\r\n          <p>Quality assurance and testing for bug-free releases.</p>\r\n        </div>\r\n\r\n        <div class=\"service-card\">\r\n          <i class=\"fas fa-phone icon-red\"></i>\r\n          <h3>Business Process Outsourcing</h3>\r\n          <p>End-to-end BPO services with 24/7 operations.</p>\r\n        </div>\r\n\r\n      </div>\r\n    </section>\r\n  </main>\r\n\r\n  <!-- Footer will be dynamically loaded here -->\r\n  <div id=\"footer\"></div>\r\n  <?php include(\"footer.php\"); ?>\r\n\r\n  \r\n\r\n</body>\r\n</html>\r\n',NULL),(41,'services','services','archived','2025-10-30 10:32:22','hello',NULL),(42,'services1','45','draft','2025-11-03 13:59:15','<!DOCTYPE html>\r\n<html lang=\"en\">\r\n<head>\r\n  <meta charset=\"UTF-8\" />\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />\r\n  <title>Chandusoft - Services</title>\r\n  <link rel=\"stylesheet\" href=\"styles.css\" />\r\n  <link\r\n    rel=\"stylesheet\"\r\n    href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css\"\r\n  />\r\n</head>\r\n<body>\r\n\r\n  <!-- Header will be dynamically loaded here -->\r\n  <div id=\"header\"></div>\r\n  <?php include(\"header.php\"); ?>\r\n\r\n  <main>\r\n    <section id=\"Services\">\r\n      <h2>Our Services</h2>\r\n      <div class=\"services-container\">\r\n\r\n        <div class=\"service-card\">\r\n          <i class=\"fas fa-building icon-blue\"></i>\r\n          <h3>Enterprise Application Solution</h3>\r\n          <p>Robust enterprise apps for seamless business operations.</p>\r\n        </div>\r\n\r\n        <div class=\"service-card\">\r\n          <i class=\"fas fa-mobile-alt icon-green\"></i>\r\n          <h3>Mobile Application Solution</h3>\r\n          <p>Cross-platform mobile apps with modern UI/UX.</p>\r\n        </div>\r\n\r\n        <div class=\"service-card\">\r\n          <i class=\"fas fa-laptop icon-black\"></i>\r\n          <h3>Web Portal Design & Solution</h3>\r\n          <p>Custom web portals for business and customer engagement.</p>\r\n        </div>\r\n\r\n        <div class=\"service-card\">\r\n          <i class=\"fas fa-tools icon-yellow\"></i>\r\n          <h3>Web Portal Maintenance & Content Management</h3>\r\n          <p>Continuous support, updates, and content handling.</p>\r\n        </div>\r\n\r\n        <div class=\"service-card\">\r\n          <i class=\"fas fa-vial icon-purple\"></i>\r\n          <h3>QA & Testing</h3>\r\n          <p>Quality assurance and testing for bug-free releases.</p>\r\n        </div>\r\n\r\n        <div class=\"service-card\">\r\n          <i class=\"fas fa-phone icon-red\"></i>\r\n          <h3>Business Process Outsourcing</h3>\r\n          <p>End-to-end BPO services with 24/7 operations.</p>\r\n        </div>\r\n\r\n      </div>\r\n    </section>\r\n  </main>\r\n\r\n  <!-- Footer will be dynamically loaded here -->\r\n  <div id=\"footer\"></div>\r\n  <?php include(\"footer.php\"); ?>\r\n\r\n  \r\n\r\n</body>\r\n</html>\r\n',NULL),(43,'about','about','draft','2025-11-03 14:18:09','hello',NULL),(44,'about us','about','published','2025-11-03 14:17:24','<div id=\"header\"></div>\r\n  <?php include(\"header.php\"); ?>\r\n<h2>About Us</h2>\r\n  <main>\r\n    \r\n    <section>\r\n      <p>\r\n        <span class=\"highlight\">Chandusoft</span> is a well-established company with over\r\n        <span class=\"highlight\">15 years</span> of experience in delivering IT and BPO solutions.\r\n        We have a team of more than <span class=\"highlight\">200 skilled professionals</span> operating\r\n        from multiple locations. One of our key strengths is <span class=\"highlight\">24/7 operations</span>,\r\n        which allows us to support clients across different time zones. We place a strong emphasis on\r\n        <span class=\"highlight\">data integrity</span> and <span class=\"highlight\">security</span>, which has helped\r\n        us earn long-term trust from our partners. Our core service areas include\r\n        <span class=\"highlight\">Software Development</span>, <span class=\"highlight\">Medical Process Services</span>,\r\n        and <span class=\"highlight\">E-Commerce Solutions</span>, all backed by a commitment to quality and process excellence.\r\n      </p>\r\n    </section>\r\n  </main>\r\n\r\n  <!-- Footer placeholder -->\r\n  <div id=\"footer\"></div>\r\n  <?php include(\"footer.php\"); ?>',NULL),(45,'harrys','test','archived','2025-11-03 14:20:26','hj',NULL);
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_settings`
--

DROP TABLE IF EXISTS `site_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `site_name` varchar(255) NOT NULL DEFAULT 'My Site',
  `site_logo` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_settings`
--

LOCK TABLES `site_settings` WRITE;
/*!40000 ALTER TABLE `site_settings` DISABLE KEYS */;
INSERT INTO `site_settings` VALUES (1,'Chandusoft','logo.jpg','2025-10-20 03:56:06');
/*!40000 ALTER TABLE `site_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','editor') NOT NULL DEFAULT 'editor',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (10,'Chaitanya','chaitanya@gmail.com','$2y$10$uEwZHEffv1xNk2X6s3YXkuV2jLe60/VCc7YqYnLdeT/dT99Xs3Wjm','editor'),(24,'sravani','sravani@gmail.com','$2y$10$eeu8enbuDy5OGoD5F/GkdunbT7wd0TEtwLAmpVj2g9SaA6yybvOuC','editor'),(25,'hari','hari@gmail.com','$2y$10$yRZaitMTv9k2YDb0ItJq9eo37vwGXRoWtyASJsHZMeYD8L6Uw81P2','editor'),(26,'saleem','saleem@gmail.com','$2y$10$anVUVMfVm4opx2YTWJ2TFeC7YXSF6AIpMi0h3rb3QsTAjRmuinzMO','editor'),(27,'keerthi','keerthi@gmail.com','$2y$10$2gGFMWg0A6cScO6aLdexN.S6sjEh8NLVfK.dVfeLg.C0/mXOy6ZGa','editor'),(28,'hello','hello@gmail.com','$2y$10$1KLSmgoqGhfsloEX0G0Z8e4yPbObsMTM65RTgm70Oe/Etgh8xTCQ2','editor'),(29,'Ramu','Ramu@gmail.com','$2y$10$I6D8UWS2SWmrvoLjWI7WseH6k6fLeiUE.9/Dq0vTvHifagZnhO8xu','admin'),(30,'pavani','pavani@gmail.com','$2y$10$lee7KyYae4JiElmjZTh9vunx/igEYH/Vnp.FvO480XDONCurXBeju','editor'),(31,'sneha','sneha@gmail.com','$2y$10$BgMsMF6aVyuUUxSkmcf1ve4Vrjv0wd3KXHpCof0LgCwMpkMkdSKCq','editor'),(32,'aisha','aisha@gmail.com','$2y$10$wibei1rCw3sB7zkVA5U4Fuh4VdfcnRy33.mxDncARLnPOQcfMKB6S','editor'),(33,'sanvi','sanvi@gmail.com','$2y$10$tdPPgYoDo7MDBkP0/JNmMecYeXFlGoxpAv.fXKeyuoOI.Ik0NJEIK','editor'),(34,'rahim','rahim@gmail.com','$2y$10$v7zkXlSqXiEOiNtmLvXHFeMjyiRIXLN6cP0/yJjeY5iozCERyS/7S','editor');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-10 15:45:20
