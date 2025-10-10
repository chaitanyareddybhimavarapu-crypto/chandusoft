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
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leads`
--

LOCK TABLES `leads` WRITE;
/*!40000 ALTER TABLE `leads` DISABLE KEYS */;
INSERT INTO `leads` VALUES (1,'Alice Smith','alice1@example.com','Hello, this is Alice 1.','2025-10-06 08:29:28',NULL),(2,'Bob Johnson','bob2@example.com','Message from Bob 2.','2025-10-06 08:29:28',NULL),(3,'Charlie Lee','charlie3@example.com','Hello from Charlie 3.','2025-10-06 08:29:28',NULL),(4,'David Brown','david4@example.com','Hi, David here 4.','2025-10-06 08:29:28',NULL),(5,'Eva Green','eva5@example.com','Eva says hello 5.','2025-10-06 08:29:28',NULL),(6,'Frank White','frank6@example.com','Message from Frank 6.','2025-10-06 08:29:28',NULL),(7,'Grace Black','grace7@example.com','Grace here, message 7.','2025-10-06 08:29:28',NULL),(8,'Hannah Scott','hannah8@example.com','Hello from Hannah 8.','2025-10-06 08:29:28',NULL),(9,'Ian Clarke','ian9@example.com','Ian\'s message 9.','2025-10-06 08:29:28',NULL),(10,'Julia Adams','julia10@example.com','Message from Julia 10.','2025-10-06 08:29:28',NULL),(11,'Kevin Young','kevin11@example.com','Hi, Kevin here 11.','2025-10-06 08:29:28',NULL),(12,'Lily King','lily12@example.com','Lily\'s message 12.','2025-10-06 08:29:28',NULL),(13,'Mark Hill','mark13@example.com','Hello from Mark 13.','2025-10-06 08:29:28',NULL),(14,'Nina Wright','nina14@example.com','Message from Nina 14.','2025-10-06 08:29:28',NULL),(15,'Oscar Turner','oscar15@example.com','Oscar says hi 15.','2025-10-06 08:29:28',NULL),(16,'Paula Martin','paula16@example.com','Message from Paula 16.','2025-10-06 08:29:28',NULL),(17,'Quinn Walker','quinn17@example.com','Quinn\'s message 17.','2025-10-06 08:29:28',NULL),(18,'Rachel Allen','rachel18@example.com','Hello from Rachel 18.','2025-10-06 08:29:28',NULL),(19,'Steve Harris','steve19@example.com','Message from Steve 19.','2025-10-06 08:29:28',NULL),(20,'Tina Nelson','tina20@example.com','Tina says hello 20.','2025-10-06 08:29:28',NULL),(21,'kavitha','kavitha@gmail.com','hello','2025-10-06 09:08:24','127.0.0.1'),(22,'ramya','ramya@gmail.com','hello','2025-10-07 06:54:23','127.0.0.1'),(23,'rahul','rahul@gmail.com','hi...','2025-10-07 10:49:32','127.0.0.1'),(24,'charan','charan@gmail.com','hello....','2025-10-07 11:30:32','127.0.0.1'),(25,'harish','harish@gmail.com','happy to see you...','2025-10-07 11:33:36','127.0.0.1'),(26,'chaitanyareddy','chaitanya@gmail.com','happy....','2025-10-07 11:46:09','127.0.0.1'),(27,'raju','raju@gmail.com','hello...','2025-10-07 12:52:02','127.0.0.1'),(28,'haris','haris@gmail.com','hello','2025-10-07 12:54:21','127.0.0.1'),(29,'hi','sivach1982@gmail.com','hello','2025-10-08 04:05:42','127.0.0.1'),(30,'rakesh','rakesh@gmail.com','good...','2025-10-08 04:17:37','127.0.0.1'),(31,'rakesh','rakesh@gmail.com','good...','2025-10-08 04:20:05','127.0.0.1'),(32,'rakesh','rakesh@gmail.com','good...','2025-10-08 04:24:44','127.0.0.1'),(33,'rama','rama@gmail.com','hello','2025-10-08 04:36:46','127.0.0.1'),(34,'chaitanyareddya','chaitanya56@gmail.com','hi','2025-10-08 04:48:15','127.0.0.1'),(35,'maneesh','maneesh@gmail.com','hello....','2025-10-08 04:51:19','127.0.0.1'),(36,'maneesh','maneesh@gmail.com','hello...','2025-10-08 04:52:46','127.0.0.1'),(37,'siva','sivach1982@gmail.com','hi','2025-10-08 12:13:28','127.0.0.1'),(38,'harry','harry@gmail.com','hello...','2025-10-08 12:40:38','127.0.0.1'),(39,'chaitanyaa','chaitanyaa@gmail.com','Hi...','2025-10-09 11:39:10','127.0.0.1');
/*!40000 ALTER TABLE `leads` ENABLE KEYS */;
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES (2,'Contact Us','contact-us','draft','2025-10-03 16:01:26',NULL),(3,'About Us','about-us','draft','2025-10-09 11:28:43','<p>This is our FAQ section.</p>'),(4,'Our Services1','our-services','draft','2025-10-07 12:45:12',NULL),(5,'test-services','test-services','draft','2025-10-07 04:29:15',NULL),(6,'our services 2','our services 2','draft','2025-10-07 07:12:10',NULL),(7,'our services 3','our services 3','draft','2025-10-08 16:31:49',NULL),(8,'about','about','draft','2025-10-09 11:28:58','hello'),(9,'hello','hello','draft','2025-10-08 18:18:09','hi'),(10,'about2','about2','draft','2025-10-08 16:44:10','<!DOCTYPE html>\r\n<html lang=\"en\">\r\n<head>\r\n  <meta charset=\"UTF-8\" />\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />\r\n  <title>About Chandusoft</title>\r\n  <link rel=\"stylesheet\" href=\"styles.css\" />\r\n  <link\r\n    rel=\"stylesheet\"\r\n    href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css\"\r\n  />\r\n</head>\r\n<body>\r\n  <!-- Header placeholder -->\r\n  <div id=\"header\"></div>\r\n  <?php include(\"header.php\"); ?>\r\n\r\n  <main>\r\n    <h2>About Us</h2>\r\n    <section>\r\n      <p>\r\n        <span class=\"highlight\">Chandusoft</span> is a well-established company with over\r\n        <span class=\"highlight\">15 years</span> of experience in delivering IT and BPO solutions.\r\n        We have a team of more than <span class=\"highlight\">200 skilled professionals</span> operating\r\n        from multiple locations. One of our key strengths is <span class=\"highlight\">24/7 operations</span>,\r\n        which allows us to support clients across different time zones. We place a strong emphasis on\r\n        <span class=\"highlight\">data integrity</span> and <span class=\"highlight\">security</span>, which has helped\r\n        us earn long-term trust from our partners. Our core service areas include\r\n        <span class=\"highlight\">Software Development</span>, <span class=\"highlight\">Medical Process Services</span>,\r\n        and <span class=\"highlight\">E-Commerce Solutions</span>, all backed by a commitment to quality and process excellence.\r\n      </p>\r\n    </section>\r\n  </main>\r\n\r\n  <!-- Footer placeholder -->\r\n  <div id=\"footer\"></div>\r\n  <?php include(\"footer.php\"); ?>\r\n\r\n</body>\r\n</html>'),(11,'about3','about3','draft','2025-10-08 16:55:25','<br />\r\n<b>Warning</b>:  Undefined variable $content_html in <b>C:\\laragon\\www\\chandusoft\\create.php</b> on line <b>196</b><br />\r\n<br />\r\n<b>Deprecated</b>:  htmlspecialchars(): Passing null to parameter #1 ($string) of type string is deprecated in <b>C:\\laragon\\www\\chandusoft\\create.php</b> on line <b>196</b><br />'),(12,'history','history','draft','2025-10-08 12:15:32',''),(13,'history','history','draft','2025-10-08 12:15:59',''),(14,'hello','hello','draft','2025-10-08 12:41:46',NULL),(15,'services','services','draft','2025-10-09 09:06:18',''),(16,'home','home','draft','2025-10-09 13:05:53','hi'),(17,'About','About','published','2025-10-09 14:48:56','<p>hi</P>'),(18,'services','services','archived','2025-10-09 14:48:06','<!DOCTYPE html>\r\n<html lang=\"en\">\r\n<head>\r\n  <meta charset=\"UTF-8\" />\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />\r\n  <title>About Chandusoft</title>\r\n  <link rel=\"stylesheet\" href=\"styles.css\" />\r\n  <link\r\n    rel=\"stylesheet\"\r\n    href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css\"\r\n  />\r\n</head>\r\n<body>\r\n  <!-- Header placeholder -->\r\n  <div id=\"header\"></div>\r\n  <?php include(\"header.php\"); ?>\r\n\r\n  <main>\r\n    <h2>About Us</h2>\r\n    <section>\r\n      <p>\r\n        <span class=\"highlight\">Chandusoft</span> is a well-established company with over\r\n        <span class=\"highlight\">15 years</span> of experience in delivering IT and BPO solutions.\r\n        We have a team of more than <span class=\"highlight\">200 skilled professionals</span> operating\r\n        from multiple locations. One of our key strengths is <span class=\"highlight\">24/7 operations</span>,\r\n        which allows us to support clients across different time zones. We place a strong emphasis on\r\n        <span class=\"highlight\">data integrity</span> and <span class=\"highlight\">security</span>, which has helped\r\n        us earn long-term trust from our partners. Our core service areas include\r\n        <span class=\"highlight\">Software Development</span>, <span class=\"highlight\">Medical Process Services</span>,\r\n        and <span class=\"highlight\">E-Commerce Solutions</span>, all backed by a commitment to quality and process excellence.\r\n      </p>\r\n    </section>\r\n  </main>\r\n\r\n  <!-- Footer placeholder -->\r\n  <div id=\"footer\"></div>\r\n  <?php include(\"footer.php\"); ?>\r\n\r\n</body>\r\n</html>\r\n'),(19,'history','history','draft','2025-10-09 13:45:42','');
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (10,'Chaitanya','chaitanya@gmail.com','$2y$10$uEwZHEffv1xNk2X6s3YXkuV2jLe60/VCc7YqYnLdeT/dT99Xs3Wjm','editor'),(24,'sravani','sravani@gmail.com','$2y$10$eeu8enbuDy5OGoD5F/GkdunbT7wd0TEtwLAmpVj2g9SaA6yybvOuC','editor'),(25,'hari','hari@gmail.com','$2y$10$yRZaitMTv9k2YDb0ItJq9eo37vwGXRoWtyASJsHZMeYD8L6Uw81P2','editor'),(26,'saleem','saleem@gmail.com','$2y$10$anVUVMfVm4opx2YTWJ2TFeC7YXSF6AIpMi0h3rb3QsTAjRmuinzMO','editor'),(27,'keerthi','keerthi@gmail.com','$2y$10$2gGFMWg0A6cScO6aLdexN.S6sjEh8NLVfK.dVfeLg.C0/mXOy6ZGa','editor'),(28,'hello','hello@gmail.com','$2y$10$1KLSmgoqGhfsloEX0G0Z8e4yPbObsMTM65RTgm70Oe/Etgh8xTCQ2','editor'),(29,'Ramu','Ramu@gmail.com','$2y$10$I6D8UWS2SWmrvoLjWI7WseH6k6fLeiUE.9/Dq0vTvHifagZnhO8xu','admin');
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

-- Dump completed on 2025-10-09 17:09:31
