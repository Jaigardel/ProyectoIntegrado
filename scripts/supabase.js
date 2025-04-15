import { createClient } from 'https://cdn.jsdelivr.net/npm/@supabase/supabase-js/+esm'

// Reemplaza estos valores con los de tu proyecto
const supabase = createClient('https://xlwpajxruqllvilzaeaa.supabase.co', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Inhsd3BhanhydXFsbHZpbHphZWFhIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDQ3MDM0MDUsImV4cCI6MjA2MDI3OTQwNX0.uXV7BYxYsAhSdkpAQkM8LEg4Fn26qE44l2Zw3kOj1Po')

// Espera a que cargue el DOM
document.addEventListener('DOMContentLoaded', () => {

const inputArchivo = document.getElementById('archivo')
const rutaInput = document.getElementById('rutaArchivo')
const img = document.getElementById('imagenMostrada')

// SUBIR imagen
document.getElementById('btnSubir').addEventListener('click', async () => {
    const archivo = inputArchivo.files[0]

    if (!archivo) {
    console.error('No se ha seleccionado ningún archivo.')
    return
    }

    const ruta = `${archivo.name}`

    const { data, error } = await supabase
    .storage
    .from('fotos') 
    .upload(ruta, archivo)

    if (error) {
    console.error('Error al subir el archivo:', error)
    } else {
    console.log('Archivo subido con éxito:', data)
    alert("Imagen subida exitosamente")
    rutaInput.value = ""
    }
})

// MOSTRAR imagen desde URL en Supabase
document.getElementById('btnMostrar').addEventListener('click', async () => {
    const ruta = rutaInput.value.trim()

    if (!ruta || "") {
    console.error('Introduce una ruta válida.')
    return
    }

    const { data, error } = supabase
    .storage
    .from('fotos') 
    .getPublicUrl(ruta)

    if (error) {
    console.error('Error al obtener URL pública:', error)
    } else {
    img.src = data.publicUrl
    }
})

// ELIMINAR imagen
document.getElementById('btnEliminar').addEventListener('click', async () => {
    const ruta = rutaInput.value.trim()
    const { data, error } = await supabase
    .storage
    .from('fotos') 
    .remove([ruta])

    if (error) {
    console.error('Error al eliminar el archivo:', error)
    } else {
    console.log('Archivo eliminado con éxito:', data)
    img.src = '' 
    }
})
})
