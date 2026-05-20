CREATE TABLE `master_supplier_bank` (
  `id`               INT           NOT NULL AUTO_INCREMENT,
  `id_supplier`      VARCHAR(50)   NOT NULL,
  `tipe_sup`         VARCHAR(5)    NOT NULL COMMENT '''S'' = Supplier, lainnya = Customer',
  `bank_currency`    VARCHAR(20)   NOT NULL,
  `bank_name`        VARCHAR(200)  NOT NULL,
  `bank_account`     VARCHAR(100)  NOT NULL,
  `beneficiary_name` VARCHAR(200)  NOT NULL,
  `status`           VARCHAR(20)   NOT NULL DEFAULT 'Active' COMMENT '''Active'' / ''Cancel''',
  `created_by`       VARCHAR(50)   DEFAULT NULL,
  `created_date`     DATETIME      DEFAULT NULL,
  `cancel_by`        VARCHAR(50)   DEFAULT NULL,
  `cancel_date`      DATETIME      DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
