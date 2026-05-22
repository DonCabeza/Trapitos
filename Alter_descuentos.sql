ALTER TABLE public.descuentos 
ADD COLUMN fecha_inicio DATE NOT NULL DEFAULT CURRENT_DATE,
ADD COLUMN fecha_fin DATE NOT NULL DEFAULT (CURRENT_DATE + INTERVAL '7 days');

-- 2. Añadir un interruptor lógico de encendido/apagado
-- Permite al administrador pausar una promoción sin tener que borrar el registro
ALTER TABLE public.descuentos 
ADD COLUMN activo BOOLEAN DEFAULT TRUE;

-- 3. Añadir una descripción o nombre de la promoción
-- Para que el empleado sepa en caja qué está aplicando (ej: 'Oferta de Primavera')
ALTER TABLE public.descuentos 
ADD COLUMN nombre_promo VARCHAR(100) NOT NULL DEFAULT 'Descuento Especial';