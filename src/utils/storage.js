export const STORAGE_KEY = 'portfoliocraft_db_v1';

export function loadPortfoliosFromStorage() {
    try {
        const data = localStorage.getItem(STORAGE_KEY);
        return data ? JSON.parse(data) : [];
    } catch (e) {
        console.error('Error loading LocalStorage DB', e);
        return [];
    }
}

export function savePortfoliosToStorage(portfoliosDB) {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(portfoliosDB));
    } catch (e) {
        console.error('Error saving LocalStorage DB', e);
    }
}
