const db = require('../db');

const usersTable = 'users';
const tokenTable = 'personal_access_tokens';

const getUserById = async (id) => {
  const sql = `SELECT *
               FROM ${usersTable}
               WHERE id = ${id}`;

  return await db.query(sql);
};

const isValidToken = async (userId, token) => {
  const sql = `SELECT id
               FROM ${tokenTable}
               WHERE tokenable_id = '${userId}'
                 AND token = '${token}'`;
  const tokenId = await db.query(sql);

  return !!tokenId;
}

const isAuth = async (userId, token) => {
  const user = await getUserById(userId);

  if (user) {
    return await isValidToken(userId, token);
  }

  return false;
};

module.exports = {
  isAuth
};