const db = require('../db');

const topicTable = 'topics';

const getFullTopicPath = async (topicId) => {
  // language=SQL format=false
  sql = `
      WITH RECURSIVE cte AS (
          SELECT *
          FROM topics
          WHERE id = ${topicId}
          UNION ALL
          SELECT p.*
          FROM cte c
          JOIN topics p ON c.parent_id = p.id
      )
      SELECT *
      FROM cte
      ORDER BY _lft;
  `;
  const topics = await db.query(sql);

  return topics.map(topic => topic.name).join('/');
}

module.exports = {
  getFullTopicPath
}