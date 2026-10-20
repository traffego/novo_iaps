-- 009_fix_duplicate_doc_types.sql
-- Normalização e remoção de duplicidades em tab_projetos_documentos_tipo

-- 1. Redirecionar documentos vinculados a IDs duplicados para os IDs 1 (Editais) e 2 (Homologações)
UPDATE `tab_projetos_documentos` d
JOIN `tab_projetos_documentos_tipo` t ON d.id_tipo_documento = t.id
SET d.id_tipo_documento = 1
WHERE t.tipo_documento = 'Editais' AND d.id_tipo_documento != 1;

UPDATE `tab_projetos_documentos` d
JOIN `tab_projetos_documentos_tipo` t ON d.id_tipo_documento = t.id
SET d.id_tipo_documento = 2
WHERE t.tipo_documento = 'Homologações' AND d.id_tipo_documento != 2;

-- 2. Apagar os IDs duplicados extras
DELETE FROM `tab_projetos_documentos_tipo` WHERE `id` NOT IN (1, 2);

-- 3. Garantir os registros principais
INSERT INTO `tab_projetos_documentos_tipo` (`id`, `tipo_documento`) VALUES (1, 'Editais')
ON DUPLICATE KEY UPDATE `tipo_documento` = 'Editais';

INSERT INTO `tab_projetos_documentos_tipo` (`id`, `tipo_documento`) VALUES (2, 'Homologações')
ON DUPLICATE KEY UPDATE `tipo_documento` = 'Homologações';

-- 4. Adicionar índice UNIQUE para evitar duplicidades futuras
ALTER TABLE `tab_projetos_documentos_tipo` ADD UNIQUE KEY `uk_tipo_documento` (`tipo_documento`);
