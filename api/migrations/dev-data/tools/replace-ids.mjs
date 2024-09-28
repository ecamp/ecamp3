import fs from "fs";
import crypto from "crypto";

function replaceIdsAndForeignKeys(sqlScript) {
  const idMap = new Map(); // Store old ID to new ID mapping

  const statements = sqlScript.split(';\n'); // Split into individual statements

  const updatedStatements = statements.map(statement => {
    // 1. Find and replace IDs within single quotes
    let matches = statement.match(/\('([^']+),?'/g);
    if (matches) {
      matches.forEach(match => {
        const oldId = match.slice(2, -1);
        if (!idMap.has(oldId)) {
          const newId = crypto.randomBytes(6).toString('hex'); // 12 random characters
          idMap.set(oldId, newId);
        }
        const newId = idMap.get(oldId);
        // console.log(`replacing ${oldId} with ${newId} in statement [${statement}]`)
        statement = statement.replace(new RegExp(`'${oldId}'`, 'g'), `'${newId}'`);
      });
    }

    // 2. Find and replace foreign keys (assuming they are not within single quotes)
    for (const [oldId, newId] of idMap) {
      statement = statement.replace(new RegExp(`\\b${oldId}\\b`, 'g'), newId);
    }

    return statement;
  });

  return updatedStatements.join(';\n') + ';';
}

const sqlScript = fs.readFileSync('only-1-camp.sql', 'utf8'); // Read your SQL script
const updatedScript = replaceIdsAndForeignKeys(sqlScript);
fs.writeFileSync('only-1-camp.sql', updatedScript); // Write the updated script to a new file
