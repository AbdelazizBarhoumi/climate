# Weather API Setup Guide

## Get Your Free OpenWeatherMap API Key

To enable real-time weather data in the tour creation form, you need to get a free API key from OpenWeatherMap.

### Steps:

1. **Sign up for a free account:**
   - Go to: https://openweathermap.org/api
   - Click "Sign Up" and create a free account
   - Verify your email address

2. **Get your API key:**
   - After logging in, go to: https://home.openweathermap.org/api_keys
   - Copy your default API key (or create a new one)
   - The free plan includes:
     - 1,000 API calls per day
     - Current weather data
     - 5-day forecast
     - Perfect for this application!

3. **Add the API key to your code:**
   - Open: `resources/views/tours/create.blade.php`
   - Find this line (around line 520):
     ```javascript
     const apiKey = 'YOUR_API_KEY_HERE'; // Replace with actual API key
     ```
   - Replace `'YOUR_API_KEY_HERE'` with your actual API key:
     ```javascript
     const apiKey = 'abc123your_actual_key_here456';
     ```

4. **Test it:**
   - Go to the tour creation page
   - Add a destination
   - Select a city from the dropdown
   - You should see real-time weather data appear!

### Features Included:

✅ **Current Weather:**
- Temperature in Celsius
- Weather condition (sunny, cloudy, rainy, etc.)
- Weather icon
- Humidity percentage
- Wind speed in km/h

✅ **24-Hour Forecast:**
- Shows temperature predictions for the next 24 hours
- Updates every 6 hours

✅ **Visual Design:**
- Beautiful gradient blue weather widget
- Real-time updates
- Auto-hides when no city is selected

### API Activation Note:

⚠️ After getting your API key, it may take **10-15 minutes** to activate. If you get errors initially, wait a bit and try again.

### Alternative: Use Environment Variable (Recommended for Production)

For better security, you can store the API key in your `.env` file:

1. Add to `.env`:
   ```
   WEATHER_API_KEY=your_api_key_here
   ```

2. Update the JavaScript code to fetch it from a backend endpoint (more secure than exposing in frontend code)

---

**Happy Weather Tracking! 🌤️**
