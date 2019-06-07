CREATE TABLE `intervals` (
  `id` int(11) UNSIGNED NOT NULL,
  `price` double(10,2) NOT NULL DEFAULT 0.00,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `intervals` (`id`, `price`, `start_date`, `end_date`) VALUES
(1, 5.00, '2019-05-01', '2019-05-05'),
(2, 10.00, '2019-05-06', '2019-05-11');

ALTER TABLE `intervals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `start_date` (`start_date`),
  ADD UNIQUE KEY `end_date` (`end_date`);

ALTER TABLE `intervals`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;
