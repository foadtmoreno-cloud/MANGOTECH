// ============================================================
// CONFIGURACIÓN DE SUPABASE - TIENDA TECH
// ============================================================

const SUPABASE_URL = 'https://nkhkqicoqcizpoqhjsph.supabase.co';
const SUPABASE_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im5raGtxaWNvcWNpenBvcWhqc3BoIiwicm9sZSI6ImFub24iLCJpYXQiOjE3ODQxMjk3NTEsImV4cCI6MjA5OTcwNTc1MX0.jREXHYKubZNmT83oQ0Eoo94gUwTLyatNYAWWe9kdM9E';

// Crear cliente Supabase global
const { createClient } = supabase;
const supabaseClient = createClient(SUPABASE_URL, SUPABASE_KEY);
