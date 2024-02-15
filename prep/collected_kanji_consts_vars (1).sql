
CREATE TABLE `collected_kanji_consts_vars` (
  `name` varchar(30) DEFAULT NULL,
  `value` int(11) DEFAULT NULL
);


INSERT INTO `collected_kanji_consts_vars` (`name`, `value`) VALUES
('sessionLength', 60),
('numberToRepeat', 500),
('reRepeatStatus', 15698),
('nextRepeatStatus', 16880);

