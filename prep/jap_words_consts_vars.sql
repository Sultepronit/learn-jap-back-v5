

CREATE TABLE `jap_words_consts_vars` (
  `name` varchar(50) DEFAULT NULL,
  `value` int(11) DEFAULT NULL
);

--
-- Дамп даних таблиці `jap_words_consts_vars`
--

INSERT INTO `jap_words_consts_vars` (`name`, `value`) VALUES
('sessionLength', 60),
('numberToRepeat', 400),
('problemDivisor', 3),
('confirmDivisor', 4),
('reRepeatStatus', 4676),
('nextRepeatStatus', 9249);
-- COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
