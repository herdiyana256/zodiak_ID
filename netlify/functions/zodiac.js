// Ubah dari import ke require
const fetch = require('node-fetch');

const zodiacSigns = [
  'aries', 'taurus', 'gemini', 'cancer', 'leo', 'virgo',
  'libra', 'scorpio', 'sagittarius', 'capricorn', 'aquarius', 'pisces'
];

exports.handler = async function(event, context) {
  try {
    const data = JSON.parse(event.body);
    const sign = data.sign ? data.sign.toLowerCase().trim() : '';
    const period = data.period ? data.period.toLowerCase().trim() : 'daily';

    if (!zodiacSigns.includes(sign)) {
      return {
        statusCode: 400,
        body: JSON.stringify({ status: 'error', message: 'Invalid zodiac sign' })
      };
    }

    const baseApiUrl = "https://horoscope-app-api.vercel.app/api/v1/get-horoscope";
    let apiEndpoint;

    switch (period) {
      case 'weekly':
        apiEndpoint = "/weekly";
        break;
      case 'monthly':
        apiEndpoint = "/monthly";
        break;
      case 'daily':
      default:
        apiEndpoint = "/daily";
        break;
    }

    let apiUrl = `${baseApiUrl}${apiEndpoint}?sign=${sign}`;

    if (period === 'daily') {
      const day = data.day ? data.day.toLowerCase().trim() : 'today';
      apiUrl += `&day=${day}`;
    }

    const response = await fetch(apiUrl);
    const horoscopeData = await response.json();

    if (!horoscopeData || !horoscopeData.data) {
      return {
        statusCode: 500,
        body: JSON.stringify({ status: 'error', message: 'No data available' })
      };
    }

    return {
      statusCode: 200,
      body: JSON.stringify({ status: 'success', horoscope_data: horoscopeData.data })
    };

  } catch (error) {
    return {
      statusCode: 500,
      body: JSON.stringify({ status: 'error', message: error.message })
    };
  }
};
