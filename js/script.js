const axios = require('axios');
const fs = require('fs');

// TMDB API Key
const API_KEY = 'b4d10555719ced3748435bc30d8b3f7b'; // Replace with your TMDB API key
const BASE_URL = 'https://api.themoviedb.org/3';

// Fetch movies from TMDB
async function fetchMovies(page = 1) {
    try {
        const response = await axios.get(`${BASE_URL}/movie/popular`, {
            params: {
                api_key: API_KEY,
                language: 'en-US',
                page: page,
            },
        });
        return response.data.results;
    } catch (error) {
        console.error('Error fetching movies:', error.message);
        return [];
    }
}

// Generate SQL INSERT statements
function generateSQL(movies) {
    let sql = '';
    movies.forEach((movie) => {
        const title = movie.title.replace(/'/g, "''");
        const description = movie.overview.replace(/'/g, "''");
        const posterPath = `https://image.tmdb.org/t/p/w500${movie.poster_path}`;
        const releaseDate = movie.release_date || 'NULL';
        const price = (Math.random() * (19.99 - 5.99) + 5.99).toFixed(2); // Random price between 5.99 and 19.99

        sql += `INSERT INTO movies (title, description, poster_path, release_date, price, created_at, updated_at) VALUES ('${title}', '${description}', '${posterPath}', '${releaseDate}', ${price}, NOW(), NOW());\n`;
    });
    return sql;
}

// Main function
async function main() {
    let allSQL = '';
    for (let page = 1; page <= 5; page++) { // Fetch 5 pages of movies (100 movies)
        console.log(`Fetching page ${page}...`);
        const movies = await fetchMovies(page);
        allSQL += generateSQL(movies);
    }

    // Save SQL to a file
    fs.writeFileSync('movies_insert.sql', allSQL, 'utf8');
    console.log('SQL file generated: movies_insert.sql');
}

document.getElementById('search-button').addEventListener('click', async () => {
    const query = document.getElementById('search-input').value;
    if (!query) return;

    try {
        const response = await fetch(`search.php?q=${encodeURIComponent(query)}`);
        const movies = await response.json();

        // Display results
        const resultsContainer = document.getElementById('search-results');
        resultsContainer.innerHTML = ''; // Clear previous results

        movies.forEach((movie) => {
            const movieCard = `
                <div class="movie-card">
                    <img src="${movie.poster_path}" alt="${movie.title}" class="movie-poster">
                    <h3>${movie.title}</h3>
                    <p>${movie.description.substring(0, 100)}...</p>
                    <p><strong>Price:</strong> €${movie.price.toFixed(2)}</p>
                </div>
            `;
            resultsContainer.innerHTML += movieCard;
        });
    } catch (error) {
        console.error('Error fetching movies:', error);
    }
});

main();