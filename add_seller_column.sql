ALTER TABLE tabel_product ADD COLUMN id_penjual INT(11) NOT NULL DEFAULT 2;
-- Optional: Add Foreign Key if Engine supports it (InnoDB)
-- ALTER TABLE tabel_product ADD CONSTRAINT fk_penjual FOREIGN KEY (id_penjual) REFERENCES tabel_penjual(id);
