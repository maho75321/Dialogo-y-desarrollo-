-- Podcasts solo-audio: subida de MP3/M4A sin video.
-- No borra ni renombra nada; solo agrega y permite url_embed opcional.
ALTER TABLE podcasts
  ADD COLUMN archivo_audio VARCHAR(255) NULL AFTER url_embed;
ALTER TABLE podcasts
  MODIFY url_embed VARCHAR(500) NULL;
