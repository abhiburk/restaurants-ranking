# Wotters

> **The city votes. You find out.**

Wotters is a community-powered restaurant discovery and daily ranking platform. Instead of relying on paid placements or static review scores, restaurants compete based on votes from the people discovering and visiting them.

Each day is a fresh competition.

## 🍽️ What is Wotters?

Wotters helps people discover restaurants that are currently getting attention in their city.

- Explore restaurants city by city
- Discover restaurant rankings
- Vote for restaurants
- Find new and trending places
- Claim a restaurant
- Follow local activity through **Food Pulse**

> **No paid rankings. No artificial promotion. Let the city decide.**

## ✨ Core Features

### 🏆 Daily Restaurant Rankings
Restaurants compete within their city based on community votes. Rankings can change based on current activity, making discovery more dynamic.

### 🗳️ Community Voting
Visitors can vote for restaurants with safeguards designed to reduce abuse:

- One vote per restaurant per day
- Cookie, device, and IP-based checks
- Rate limiting
- Suspicious activity detection
- Additional verification rules as the platform evolves

### 🔎 Restaurant Discovery
Users can browse restaurants by city, search for places, view restaurant details, and explore rankings and trending activity.

### 📍 City-Based Communities
Local contributors can join city communities and help expand the restaurant directory with their local knowledge.

Contributor applications can include:

- Why they want to contribute
- Areas or cities they know well
- Relevant local knowledge

### 🤝 Community Contributors
Approved contributors can:

- Add restaurants
- Submit local information
- Improve restaurant discovery
- Earn points and build their reputation

### 🥇 Contributor Leaderboard
Contributors can track:

- Total points
- Monthly points
- Contributor level
- Quality score
- Recent activity
- Point history
- Progress toward the next level

### 🏪 Restaurant Submissions
Contributors and approved partners can submit restaurants for moderation before they become publicly available.

### 🏷️ Restaurant Claiming
Restaurant owners or representatives can claim their business and manage restaurant information after approval.

### ⚡ Food Pulse
Food Pulse is Wotters' local activity feed. It can highlight:

- New restaurants added
- Restaurants gaining momentum
- Recent votes and ranking movement
- Contributor activity
- Newly approved restaurants

The goal is to make restaurant discovery feel alive and connected to what is happening in the city.

## 🧑‍🤝‍🧑 Platform Roles

| Role | Capabilities |
| --- | --- |
| **Visitor** | Browse, search, discover, vote, and view rankings |
| **Contributor** | Join communities, submit restaurants, earn points, and track progress |
| **Restaurant Owner / Partner** | Claim or add restaurants and manage business information |
| **Administrator** | Moderate applications, submissions, claims, cities, and platform activity |

## 🛠️ Tech Stack

Wotters is currently being built with:

- **Laravel**
- **PHP**
- **MySQL**
- **Vue.js**
- **Inertia.js**
- **Tailwind CSS**
- **Filament**
- **Laravel Queues & Jobs**
- **Caching**

## 🗺️ Project Status

### Currently built

- [x] Authentication
- [x] City-wise restaurant listing
- [x] Restaurant detail pages
- [x] Voting system
- [x] Restaurant claiming
- [x] Coming soon cities
- [x] Contributor communities
- [x] Contributor points and progression
- [x] Basic admin panel
- [x] Contributor panel
- [x] Basic restaurant partner/owner panel
- [x] Restaurant submission workflow

### Next

- [ ] Food Pulse
- [ ] Improved anti-fraud voting
- [ ] Contributor leaderboard improvements
- [ ] Restaurant analytics
- [ ] Search and discovery improvements
- [ ] Crowd and activity visualization
- [ ] Notifications and activity updates
- [ ] Mobile experience improvements

## 🎯 Vision

Wotters is starting with restaurant discovery and daily rankings, but the bigger goal is to build a living local discovery platform.

A place where people can answer:

- What restaurants are people talking about today?
- Which places are trending right now?
- What is popular in my city?
- Which hidden gems are worth discovering?
- Where is the activity happening?

Instead of relying only on permanent ratings, Wotters focuses on **what is happening now**.

## 🚀 Getting Started

```bash
git clone <repository-url>
cd wotters

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Configure your database credentials in `.env`, then run:

```bash
php artisan migrate:fresh --seed (Seed only when dummy data is needed)
npm run dev
php artisan serve
```

## 🤝 Contributing

Wotters is an evolving project. Feedback and ideas around local discovery, restaurant data, community participation, ranking systems, and user experience are welcome.

## 📌 Build in Public

Wotters is being developed openly as a product journey — from idea and MVP to a larger local discovery platform.

**The city votes. You find out.**

---

© Wotters
