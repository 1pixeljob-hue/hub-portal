import { useState, useEffect } from 'react';
import { categories, mockLinks } from './data/mockData';

const Sidebar = () => (
  <aside className="hidden md:flex w-64 flex-col border-r border-border-light/50 dark:border-border-dark/50 bg-surface-light/80 dark:bg-surface-dark/80 backdrop-blur-md transition-colors duration-300">
    <div className="flex h-16 items-center gap-3 px-6 border-b border-border-light/50 dark:border-border-dark/50">
      <div className="flex items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 p-1.5 shadow-lg shadow-indigo-500/20">
        <span className="material-symbols-outlined text-[24px] text-white">link</span>
      </div>
      <h1 className="text-lg font-bold tracking-tight bg-gradient-to-r from-indigo-600 to-pink-500 bg-clip-text text-transparent dark:from-indigo-400 dark:to-pink-400">Link Manager</h1>
    </div>
    <div className="flex flex-1 flex-col justify-between overflow-y-auto p-4">
      <nav className="flex flex-col gap-1">
        <div className="px-2 py-2">
          <p className="text-xs font-semibold uppercase tracking-wider text-text-secondary-light dark:text-text-secondary-dark">Dashboard</p>
        </div>
        <a className="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-white bg-gradient-to-r from-indigo-500 to-purple-600 shadow-md shadow-indigo-500/20 transition-all hover:shadow-lg hover:shadow-indigo-500/30 hover:scale-[1.02]" href="#">
          <span className="material-symbols-outlined text-[20px]">dashboard</span>
          All Links
        </a>

        <div className="mt-4 px-2 py-2">
          <p className="text-xs font-semibold uppercase tracking-wider text-text-secondary-light dark:text-text-secondary-dark">Categories</p>
        </div>
        {categories.map(cat => (
          <a key={cat.id} className="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark hover:bg-surface-light-highlight dark:hover:bg-surface-dark-highlight hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" href="#">
            <span className={`material-symbols-outlined text-[20px] group-hover:text-${cat.colorClass.split('-')[1]}-500 transition-colors`}>{cat.icon}</span>
            {cat.name}
            <span className={`ml-auto rounded-full ${cat.bgClass} px-2 py-0.5 text-xs font-medium ${cat.colorClass}`}>{cat.count}</span>
          </a>
        ))}
      </nav>

      <div className="flex flex-col gap-4">
        <div className="rounded-lg bg-surface-light-highlight/50 dark:bg-surface-dark/50 backdrop-blur-sm p-4 border border-border-light/50 dark:border-border-dark/50">
          <div className="flex items-center gap-3">
            <div className="h-10 w-10 overflow-hidden rounded-full ring-2 ring-white dark:ring-surface-dark" style={{ background: 'linear-gradient(135deg, #6366f1 0%, #ec4899 100%)' }}></div>
            <div className="flex flex-col">
              <span className="text-sm font-bold text-text-primary-light dark:text-white">Alex Morgan</span>
              <span className="text-xs font-medium text-indigo-500 dark:text-indigo-400">Pro Plan</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </aside>
);

const Header = ({ toggleTheme, showAddLinkModal, isDark }: { toggleTheme: () => void, showAddLinkModal: () => void, isDark: boolean }) => (
  <header className="flex h-16 shrink-0 items-center justify-between border-b border-border-light/50 dark:border-border-dark/50 bg-surface-light/80 dark:bg-surface-dark/80 backdrop-blur-md px-6 transition-colors duration-300">
    <div className="flex flex-1 items-center max-w-md">
      <div className="relative w-full group">
        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-text-secondary-light dark:text-text-secondary-dark group-focus-within:text-indigo-500 transition-colors">
          <span className="material-symbols-outlined text-[20px]">search</span>
        </div>
        <input className="block w-full rounded-full border-2 border-transparent bg-surface-light-highlight dark:bg-surface-dark-highlight py-2 pl-10 pr-3 text-sm text-text-primary-light dark:text-white placeholder-text-secondary-light dark:placeholder-text-secondary-dark focus:border-indigo-500 focus:bg-white dark:focus:bg-surface-dark focus:outline-none focus:ring-0 transition-all shadow-inner" placeholder="Search links, tags, or collections..." type="text" />
      </div>
    </div>
    <div className="flex items-center gap-4">
      <button className="relative rounded-lg p-2 text-text-secondary-light dark:text-text-secondary-dark hover:bg-surface-light-highlight dark:hover:bg-surface-dark-highlight hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" onClick={toggleTheme} title="Toggle Theme">
        <span className="material-symbols-outlined text-[24px]">{isDark ? 'light_mode' : 'dark_mode'}</span>
      </button>
      <div className="h-6 w-px bg-border-light dark:bg-border-dark mx-1"></div>
      <button className="relative rounded-lg p-2 text-text-secondary-light dark:text-text-secondary-dark hover:bg-surface-light-highlight dark:hover:bg-surface-dark-highlight hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
        <span className="material-symbols-outlined text-[24px]">notifications</span>
        <span className="absolute top-2 right-2 h-2.5 w-2.5 rounded-full bg-pink-500 ring-2 ring-surface-light dark:ring-surface-dark animate-pulse"></span>
      </button>
      <button className="flex items-center gap-2 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 hover:scale-[1.02] transition-all" onClick={showAddLinkModal}>
        <span className="material-symbols-outlined text-[20px]">add</span>
        Add New Link
      </button>
    </div>
  </header>
);

const CardActionMenu = ({ showMenu, setShowMenu }: { showMenu: boolean, setShowMenu: (s: boolean) => void }) => {
  if (!showMenu) return null;
  return (
    <div className="link-action-menu absolute right-0 mt-1 w-32 origin-top-right rounded-lg bg-white dark:bg-surface-dark shadow-xl ring-1 ring-black/5 dark:ring-white/10 overflow-hidden z-30 border border-border-light dark:border-border-dark">
      <button className="flex w-full items-center gap-2 px-3 py-2 text-sm text-text-secondary-light dark:text-text-secondary-dark hover:bg-surface-light-highlight dark:hover:bg-surface-dark-highlight hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" onClick={() => setShowMenu(false)}>
        <span className="material-symbols-outlined text-[18px]">edit</span>
        Edit
      </button>
      <button className="flex w-full items-center gap-2 px-3 py-2 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" onClick={() => setShowMenu(false)}>
        <span className="material-symbols-outlined text-[18px]">delete</span>
        Delete
      </button>
    </div>
  );
};

const LinkCard = ({ link }: { link: any }) => {
  const [showMenu, setShowMenu] = useState(false);

  // Click outside to close menu handler could go here for perfection

  return (
    <div className="group interactive-card">
      <div className="interactive-card-inner flex flex-col justify-between p-5">
        <div className={`absolute top-0 left-0 h-1 w-full bg-gradient-to-r ${link.theme === 'gray' ? 'from-gray-600 to-black dark:from-gray-400 dark:to-white' : `from-${link.theme}-400 to-${link.theme}-600`} opacity-0 group-hover:opacity-100 transition-opacity rounded-t-xl z-10`}></div>
        <div>
          <div className="mb-4 flex items-start justify-between">
            {link.logoUrl && (
              <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-white dark:bg-surface-dark-highlight p-2 border border-border-light/50 dark:border-white/5 shadow-sm relative z-10">
                <img src={link.logoUrl} alt="Logo" className="h-8 w-8" />
              </div>
            )}
            {link.initial && (
              <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 p-2 text-white font-bold text-xl shadow-md shadow-indigo-500/20 relative z-10">
                {link.initial}
              </div>
            )}
            {link.initialIcon && (
              <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-neutral-800 p-2 text-white shadow-md shadow-black/20 relative z-10">
                <span className="material-symbols-outlined text-[32px]">{link.initialIcon}</span>
              </div>
            )}

            <div className="relative z-20">
              <button className="rounded-lg p-1.5 text-text-secondary-light dark:text-text-secondary-dark hover:bg-surface-light-highlight dark:hover:bg-surface-dark-highlight transition-all" onClick={(e) => { e.stopPropagation(); setShowMenu(!showMenu); }}>
                <span className="material-symbols-outlined text-[20px]">more_vert</span>
              </button>
              <CardActionMenu showMenu={showMenu} setShowMenu={setShowMenu} />
            </div>
          </div>

          <h3 className={`mb-1 text-lg font-bold text-text-primary-light dark:text-white group-hover:text-${link.theme}-600 dark:group-hover:text-${link.theme}-400 transition-colors`}>{link.title}</h3>
          <p className="mb-4 truncate text-sm text-text-secondary-light dark:text-text-secondary-dark">{link.url}</p>

          <div className="flex flex-wrap gap-2">
            {link.tags.map((tag: any, index: number) => (
              <span key={index} className={`rounded-full bg-${tag.color}-50 dark:bg-${tag.color}-900/20 px-2.5 py-1 text-xs font-semibold text-${tag.color}-600 dark:text-${tag.color}-400 border border-${tag.color}-100 dark:border-${tag.color}-800/30`}>
                {tag.name}
              </span>
            ))}
          </div>
        </div>

        <div className="mt-4 border-t border-border-light dark:border-white/5 pt-4 relative z-10">
          <a className="refined-btn flex w-full items-center justify-center gap-2 rounded-lg bg-surface-light-highlight dark:bg-surface-dark-highlight py-2 text-sm font-medium text-text-primary-light dark:text-white transition-all opacity-40 group-hover:opacity-100 bg-white/10 backdrop-blur-sm" href="#">
            Open Link
            <span className="material-symbols-outlined text-[16px]">open_in_new</span>
          </a>
        </div>
      </div>
    </div>
  );
}

export default function App() {
  const [isDark, setIsDark] = useState(true);
  const [time, setTime] = useState('');
  const [greeting, setGreeting] = useState('');
  const [modalOpen, setModalOpen] = useState(false);

  useEffect(() => {
    if (isDark) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  }, [isDark]);

  useEffect(() => {
    const updateTime = () => {
      const now = new Date();
      const hours = now.getHours();
      const minutes = String(now.getMinutes()).padStart(2, '0');
      setTime(`${hours}:${minutes}`);

      let currentGreeting = 'Good morning';
      if (hours >= 12 && hours < 18) currentGreeting = 'Good afternoon';
      else if (hours >= 18) currentGreeting = 'Good evening';
      setGreeting(`${currentGreeting}, Alex`);
    };
    updateTime();
    const interval = setInterval(updateTime, 1000);
    return () => clearInterval(interval);
  }, []);

  return (
    <div className="bg-background-light dark:bg-background-dark font-display text-text-primary-light dark:text-text-primary-dark antialiased overflow-hidden transition-colors duration-300 selection:bg-pink-500 selection:text-white">
      <div className="fixed inset-0 pointer-events-none bg-mesh-vibrant z-0 border opacity-100 dark:opacity-40"></div>
      <div className="flex h-screen w-full relative z-10">
        <Sidebar />
        <main className="flex h-full flex-1 flex-col overflow-hidden bg-transparent transition-colors duration-300">
          <Header toggleTheme={() => setIsDark(!isDark)} showAddLinkModal={() => setModalOpen(true)} isDark={isDark} />

          <div className="flex-1 overflow-y-auto p-4 md:p-8">
            <div className="mx-auto max-w-7xl">
              {/* Greeting Banner */}
              <div className="relative mb-8 overflow-hidden rounded-2xl p-[2px] bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 shadow-xl shadow-purple-500/10">
                <div className="relative flex flex-col md:flex-row md:items-end justify-between gap-4 rounded-2xl bg-white/90 dark:bg-surface-dark/90 backdrop-blur-xl p-6 md:p-8">
                  <div className="z-10">
                    <h2 className="text-3xl md:text-4xl font-extrabold tracking-tight bg-gradient-to-r from-indigo-600 to-pink-500 bg-clip-text text-transparent dark:from-indigo-400 dark:to-pink-400 drop-shadow-sm">{greeting}</h2>
                    <p className="text-text-secondary-light dark:text-text-secondary-dark mt-2 font-medium">Here's what's happening with your links today.</p>
                  </div>
                  <div className="z-10 flex items-center gap-3 rounded-xl bg-surface-light-highlight/50 dark:bg-surface-dark-highlight/50 p-3 backdrop-blur-md border border-white/20 dark:border-white/5">
                    <div className="rounded-lg bg-indigo-100 dark:bg-indigo-500/20 p-2 text-indigo-600 dark:text-indigo-400">
                      <span className="material-symbols-outlined text-[28px]">schedule</span>
                    </div>
                    <span className="digital-clock text-4xl font-light tracking-tight text-text-primary-light dark:text-white tabular-nums">{time}</span>
                  </div>
                  <div className="absolute -top-24 -right-24 h-64 w-64 rounded-full bg-purple-500/20 blur-3xl"></div>
                  <div className="absolute -bottom-24 -left-24 h-64 w-64 rounded-full bg-indigo-500/20 blur-3xl"></div>
                </div>
              </div>

              {/* Summary Cards */}
              <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                {/* Favorites */}
                <div className="group relative overflow-hidden rounded-xl border border-white/50 dark:border-white/10 bg-white/60 dark:bg-surface-dark/60 p-4 shadow-sm hover:shadow-md transition-all backdrop-blur-sm">
                  <div className="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                  <div className="relative flex items-center gap-4">
                    <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 text-white shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform">
                      <span className="material-symbols-outlined">star</span>
                    </div>
                    <div>
                      <p className="text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark">Favorites</p>
                      <h3 className="text-2xl font-bold text-text-primary-light dark:text-white">12</h3>
                    </div>
                  </div>
                </div>
                {/* Recent */}
                <div className="group relative overflow-hidden rounded-xl border border-white/50 dark:border-white/10 bg-white/60 dark:bg-surface-dark/60 p-4 shadow-sm hover:shadow-md transition-all backdrop-blur-sm">
                  <div className="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                  <div className="relative flex items-center gap-4">
                    <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 text-white shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform">
                      <span className="material-symbols-outlined">schedule</span>
                    </div>
                    <div>
                      <p className="text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark">Recent</p>
                      <h3 className="text-2xl font-bold text-text-primary-light dark:text-white">8</h3>
                    </div>
                  </div>
                </div>
                {/* Collections */}
                <div className="group relative overflow-hidden rounded-xl border border-white/50 dark:border-white/10 bg-white/60 dark:bg-surface-dark/60 p-4 shadow-sm hover:shadow-md transition-all backdrop-blur-sm">
                  <div className="absolute inset-0 bg-gradient-to-br from-purple-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                  <div className="relative flex items-center gap-4">
                    <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-purple-400 to-purple-600 text-white shadow-lg shadow-purple-500/30 group-hover:scale-110 transition-transform">
                      <span className="material-symbols-outlined">folder</span>
                    </div>
                    <div>
                      <p className="text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark">Collections</p>
                      <h3 className="text-2xl font-bold text-text-primary-light dark:text-white">5</h3>
                    </div>
                  </div>
                </div>
                {/* Broken Links */}
                <div className="group relative overflow-hidden rounded-xl border border-white/50 dark:border-white/10 bg-white/60 dark:bg-surface-dark/60 p-4 shadow-sm hover:shadow-md transition-all backdrop-blur-sm">
                  <div className="absolute inset-0 bg-gradient-to-br from-orange-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                  <div className="relative flex items-center gap-4">
                    <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-orange-400 to-orange-600 text-white shadow-lg shadow-orange-500/30 group-hover:scale-110 transition-transform">
                      <span className="material-symbols-outlined">broken_image</span>
                    </div>
                    <div>
                      <p className="text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark">Broken Links</p>
                      <h3 className="text-2xl font-bold text-text-primary-light dark:text-white">0</h3>
                    </div>
                  </div>
                </div>
              </div>

              {/* All Links List */}
              <div className="mb-6 flex flex-wrap items-center justify-between gap-4">
                <h2 className="text-2xl font-bold text-text-primary-light dark:text-white">All Links</h2>
                <div className="flex items-center gap-2">
                  <div className="flex items-center rounded-lg border border-border-light dark:border-border-dark bg-white dark:bg-surface-dark p-1 transition-colors shadow-sm">
                    <button className="rounded p-1.5 text-text-secondary-light dark:text-text-secondary-dark hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-900/30 dark:hover:text-indigo-400 transition-colors">
                      <span className="material-symbols-outlined text-[20px]">view_list</span>
                    </button>
                    <button className="rounded bg-indigo-50 dark:bg-indigo-500/20 p-1.5 text-indigo-600 dark:text-indigo-400 shadow-sm">
                      <span className="material-symbols-outlined text-[20px]">grid_view</span>
                    </button>
                  </div>
                  <button className="flex items-center gap-2 rounded-lg border border-border-light dark:border-border-dark bg-white dark:bg-surface-dark px-3 py-2 text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark hover:border-indigo-300 dark:hover:border-indigo-700 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all shadow-sm hover:shadow-md">
                    <span className="material-symbols-outlined text-[18px]">filter_list</span>
                    Filter
                  </button>
                  <button className="flex items-center gap-2 rounded-lg border border-border-light dark:border-border-dark bg-white dark:bg-surface-dark px-3 py-2 text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark hover:border-indigo-300 dark:hover:border-indigo-700 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all shadow-sm hover:shadow-md">
                    <span className="material-symbols-outlined text-[18px]">sort</span>
                    Sort
                  </button>
                </div>
              </div>

              <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3">
                {mockLinks.map(link => (
                  <LinkCard key={link.id} link={link} />
                ))}

                <button className="group flex min-h-[220px] flex-col items-center justify-center rounded-xl border-2 border-dashed border-indigo-200 dark:border-indigo-800/40 bg-white/30 dark:bg-surface-dark/30 p-5 transition-all hover:border-indigo-400 dark:hover:border-indigo-500 hover:bg-indigo-50/50 dark:hover:bg-indigo-900/10 backdrop-blur-sm" onClick={() => setModalOpen(true)}>
                  <div className="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-white dark:bg-surface-dark-highlight text-indigo-300 dark:text-indigo-700 group-hover:bg-gradient-to-r group-hover:from-indigo-500 group-hover:to-purple-600 group-hover:text-white transition-all shadow-sm group-hover:shadow-lg group-hover:shadow-indigo-500/30 group-hover:scale-110">
                    <span className="material-symbols-outlined text-[36px]">add</span>
                  </div>
                  <h3 className="text-lg font-bold text-text-secondary-light dark:text-text-secondary-dark group-hover:text-indigo-600 dark:group-hover:text-indigo-400">Add New Link</h3>
                  <p className="text-sm text-text-secondary-light/70 dark:text-text-secondary-dark/70">Create a new bookmark</p>
                </button>
              </div>
            </div>
          </div>
        </main>
      </div>

      {/* Add Link Modal */}
      {modalOpen && (
        <div className="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 animate-[fadeIn_0.2s_ease-out]">
          <div className="relative w-full max-w-[900px] flex flex-col bg-surface-light dark:bg-surface-dark rounded-2xl shadow-2xl border border-border-light/50 dark:border-border-dark/50 overflow-hidden max-h-[95vh]">
            <div className="flex items-center justify-between px-6 py-5 border-b border-border-light/50 dark:border-border-dark/50 bg-white/50 dark:bg-surface-dark/50">
              <h3 className="text-xl font-bold text-text-primary-light dark:text-white">Add New Link</h3>
              <button className="text-text-secondary-light hover:text-text-primary-light dark:text-text-secondary-dark dark:hover:text-white transition-colors p-1 rounded-lg" onClick={() => setModalOpen(false)}>
                <span className="material-symbols-outlined">close</span>
              </button>
            </div>
            <div className="flex flex-col md:flex-row h-full overflow-hidden">
              {/* Live Preview Left */}
              <div className="w-full md:w-[320px] bg-surface-light-highlight/50 dark:bg-black/20 border-b md:border-b-0 md:border-r border-border-light/50 dark:border-border-dark/50 flex flex-col p-6 shrink-0">
                <label className="block text-sm font-semibold text-text-secondary-light dark:text-text-secondary-dark mb-3">Live Preview</label>
                <div className="flex-1 min-h-[240px] md:min-h-0 bg-white dark:bg-surface-dark rounded-xl border border-border-light/50 dark:border-border-dark/50 shadow-sm relative overflow-hidden">
                  <div className="absolute top-0 left-0 right-0 h-8 bg-surface-light-highlight dark:bg-surface-dark-highlight border-b border-border-light/50 dark:border-border-dark/50 flex items-center px-3 gap-2 z-10">
                    <div className="flex gap-1.5">
                      <div className="w-2.5 h-2.5 rounded-full bg-red-400/80"></div>
                      <div className="w-2.5 h-2.5 rounded-full bg-amber-400/80"></div>
                      <div className="w-2.5 h-2.5 rounded-full bg-emerald-400/80"></div>
                    </div>
                    <div className="flex-1 bg-white/50 dark:bg-black/20 h-5 rounded text-[10px] text-text-secondary-light dark:text-text-secondary-dark flex items-center px-2 truncate">
                      example.com
                    </div>
                  </div>
                  <div className="absolute inset-0 top-8 flex flex-col items-center justify-center p-4">
                    <div className="flex flex-col items-center text-center gap-2 w-full">
                      <div className="w-12 h-12 bg-indigo-100 dark:bg-indigo-500/20 rounded-xl flex items-center justify-center text-2xl mb-2">🌎</div>
                      <h4 className="font-bold text-text-primary-light dark:text-white text-sm leading-tight">Example Website - The Best Resources</h4>
                      <p className="text-xs text-text-secondary-light dark:text-text-secondary-dark line-clamp-3">
                        Discover the latest tools, design inspiration, and coding resources for modern web development.
                      </p>
                    </div>
                  </div>
                </div>
                <div className="mt-4 space-y-3">
                  <div className="flex items-center gap-3 text-xs text-text-secondary-light dark:text-text-secondary-dark">
                    <span className="flex items-center gap-1.5">
                      <span className="material-symbols-outlined text-[16px] text-emerald-500">check_circle</span> HTTPS Valid
                    </span>
                    <span className="flex items-center gap-1.5">
                      <span className="material-symbols-outlined text-[16px] text-emerald-500">check_circle</span> Metadata Found
                    </span>
                  </div>
                </div>
              </div>
              {/* Form Right */}
              <div className="flex-1 flex flex-col min-w-0">
                <div className="p-6 overflow-y-auto flex-1">
                  <div className="mb-6">
                    <label className="block text-sm font-semibold text-text-primary-light dark:text-white mb-2">URL</label>
                    <div className="relative flex items-center">
                      <input className="w-full bg-surface-light-highlight dark:bg-surface-dark-highlight border border-border-light dark:border-border-dark rounded-xl px-4 py-3 pl-11 text-text-primary-light dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all" placeholder="https://example.com" type="url" defaultValue="https://example.com" />
                      <div className="absolute left-3 text-text-secondary-light dark:text-text-secondary-dark flex items-center">
                        <span className="material-symbols-outlined text-[20px]">link</span>
                      </div>
                      <button className="absolute right-2 px-3 py-1.5 text-xs font-semibold text-primary hover:bg-primary/10 rounded-lg transition-colors flex items-center gap-1">
                        <span className="material-symbols-outlined text-[16px]">refresh</span> Refetch
                      </button>
                    </div>
                  </div>
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                      <label className="block text-sm font-semibold text-text-primary-light dark:text-white mb-2">Title</label>
                      <input className="w-full bg-surface-light-highlight dark:bg-surface-dark-highlight border border-border-light dark:border-border-dark rounded-xl px-4 py-3 text-text-primary-light dark:text-white focus:ring-2 focus:ring-primary outline-none" type="text" defaultValue="Example Website" />
                    </div>
                    <div>
                      <label className="block text-sm font-semibold text-text-primary-light dark:text-white mb-2">Category</label>
                      <div className="relative">
                        <select className="w-full bg-surface-light-highlight dark:bg-surface-dark-highlight border border-border-light dark:border-border-dark rounded-xl px-4 py-3 pr-10 appearance-none text-text-primary-light dark:text-white focus:ring-2 focus:ring-primary outline-none cursor-pointer">
                          <option>Design Inspiration</option>
                          <option>Development Tools</option>
                        </select>
                        <div className="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-text-secondary-light dark:text-text-secondary-dark">
                          <span className="material-symbols-outlined">expand_more</span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div className="mb-6">
                    <label className="block text-sm font-semibold text-text-primary-light dark:text-white mb-2">Tags</label>
                    <div className="p-3 bg-surface-light-highlight dark:bg-surface-dark-highlight border border-border-light dark:border-border-dark rounded-xl min-h-[56px] flex flex-wrap items-center gap-2">
                      <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800/30">
                        tech <span className="material-symbols-outlined text-[14px] cursor-pointer">close</span>
                      </span>
                      <input className="bg-transparent border-none outline-none text-sm text-text-primary-light dark:text-white placeholder-text-secondary-light dark:placeholder-text-secondary-dark flex-1 min-w-[120px]" placeholder="Type and press Enter..." type="text" />
                    </div>
                  </div>
                  <div>
                    <label className="block text-sm font-semibold text-text-primary-light dark:text-white mb-2">Description <span className="text-text-secondary-light font-normal ml-1">(Optional)</span></label>
                    <textarea className="w-full bg-surface-light-highlight dark:bg-surface-dark-highlight border border-border-light dark:border-border-dark rounded-xl px-4 py-3 text-text-primary-light dark:text-white focus:ring-2 focus:ring-primary outline-none resize-none h-24" placeholder="Add a short description..."></textarea>
                  </div>
                </div>
                <div className="bg-surface-light-highlight/30 dark:bg-black/20 px-6 py-4 flex items-center justify-end gap-3 border-t border-border-light/50 dark:border-border-dark/50 shrink-0">
                  <button className="px-5 py-2.5 rounded-xl text-sm font-semibold text-text-secondary-light dark:text-text-secondary-dark hover:bg-surface-light-highlight dark:hover:bg-surface-dark-highlight transition-colors" onClick={() => setModalOpen(false)}>
                    Cancel
                  </button>
                  <button className="flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 hover:scale-[1.02] transition-all">
                    <span className="material-symbols-outlined text-[20px]">add</span>
                    Add Link
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
