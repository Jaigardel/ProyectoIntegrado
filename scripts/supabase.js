import { createClient } from 'https://cdn.jsdelivr.net/npm/@supabase/supabase-js/+esm'

// Reemplaza estos valores con los de tu proyecto
const supabase = createClient('https://xlwpajxruqllvilzaeaa.supabase.co', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Inhsd3BhanhydXFsbHZpbHphZWFhIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDQ3MDM0MDUsImV4cCI6MjA2MDI3OTQwNX0.uXV7BYxYsAhSdkpAQkM8LEg4Fn26qE44l2Zw3kOj1Po')

// Función para subir una imagen
export async function subirImagen(archivo) {
  if (!archivo) {
    console.error('No se ha seleccionado ningún archivo.')
    return { error: 'No se ha seleccionado ningún archivo.' }
  }

  const ruta = `${archivo.name}`

  const { data, error } = await supabase
    .storage
    .from('fotos')
    .upload(ruta, archivo)

  if (error) {
    console.error('Error al subir el archivo:', error)
    return { error }
  } else {
    console.log('Archivo subido con éxito:', data)
    return { data }
  }
}

// Función para obtener la URL pública de una imagen
export async function obtenerUrlPublica(ruta) {
  if (!ruta || ruta.trim() === "") {
    console.error('Introduce una ruta válida.')
    return { error: 'Introduce una ruta válida.' }
  }

  const { data, error } = supabase
    .storage
    .from('fotos')
    .getPublicUrl(ruta)

  if (error) {
    console.error('Error al obtener URL pública:', error)
    return { error }
  } else {
    return { data }
  }
}

// Función para eliminar una imagen
export async function eliminarImagen(ruta) {
  if (!ruta || ruta.trim() === "") {
    console.error('Introduce una ruta válida para eliminar.')
    return { error: 'Introduce una ruta válida para eliminar.' }
  }

  const { data, error } = await supabase
    .storage
    .from('fotos')
    .remove([ruta])

  if (error) {
    console.error('Error al eliminar el archivo:', error)
    return { error }
  } else {
    console.log('Archivo eliminado con éxito:', data)
    return { data }
  }
}
