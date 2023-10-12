import { ChartBar, ChartLine, Main } from '@duxweb/dux-refine'
import { useCustom, useGo, useList, useTranslate } from '@refinedev/core'
import { Card, Link, Skeleton } from 'tdesign-react/esm'
import clsx from 'clsx'

const Index = () => {
  const translate = useTranslate()

  const { data: statsData } = useCustom<Record<string, any>>({
    method: 'get',
    url: 'cms/stats/index',
  })

  const { data } = useList<Record<string, any>>({
    resource: 'content.article',
    meta: {
      params: {
        pageSize: 5,
      },
    },
  })

  const nums = statsData?.data?.nums
  const views = statsData?.data?.views
  const push = statsData?.data?.push
  const classify = statsData?.data?.class
  const spider = statsData?.data?.spider
  const browsers: Array<Record<string, any>> = statsData?.data?.browsers
  const ips: Array<Record<string, any>> = statsData?.data?.ips

  return (
    <Main title={translate('cms.dashboard.name')} icon='i-tabler:home'>
      <div className='flex flex-col gap-4 lg:flex-row'>
        <div className='grid grid-cols-2 flex-none gap-4'>
          <CardItem
            color='brand'
            title={translate('cms.dashboard.nums.article')}
            num={nums?.article}
            icon='i-tabler:article'
          />
          <CardItem
            color='success'
            title={translate('cms.dashboard.nums.pv')}
            num={nums?.pv}
            icon='i-tabler:eye'
          />
          <CardItem
            color='error'
            title={translate('cms.dashboard.nums.uv')}
            num={nums?.uv}
            icon='i-tabler:user'
          />
          <CardItem
            color='warning'
            title={translate('cms.dashboard.nums.spider')}
            num={nums?.spider}
            icon='i-tabler:spider'
          />
        </div>
        <div className='grid-colos-1 grid gap-4 lg:grid-cols-2 lg:w-0 lg:flex-1'>
          <Card title={translate('cms.dashboard.views.name')}>
            <div className='h-61 w-full'>
              <ChartLine
                labels={views?.labels}
                data={[
                  { name: translate('cms.dashboard.views.pv'), data: views?.pvs || [] },
                  { name: translate('cms.dashboard.views.uv'), data: views?.uvs || [] },
                ]}
                legend
              />
            </div>
          </Card>
          <Card title={translate('cms.dashboard.spider')}>
            <div className='h-61 w-full'>
              <ChartLine labels={spider?.labels} data={spider?.data} legend />
            </div>
          </Card>
        </div>
      </div>
      <div className='mt-4 flex flex-col gap-4 gap-4 lg:flex-row'>
        <div className='lg:w-84'>
          <Card title={translate('cms.dashboard.source')} headerBordered>
            <div className='w-full flex flex-col gap-4'>
              <Skeleton theme={'paragraph'} loading={!browsers}>
                {(browsers &&
                  browsers.length > 0 &&
                  browsers?.map?.((item, key) => (
                    <Browser key={key} name={item?.browser} num={item?.num} />
                  ))) || <Empty />}
              </Skeleton>
            </div>
          </Card>
          <Card title={translate('cms.dashboard.area')} className='mt-4' headerBordered>
            <div className='w-full flex flex-col gap-4'>
              <Skeleton theme={'paragraph'} loading={!ips}>
                {(ips &&
                  ips.length > 0 &&
                  ips.length > 0 &&
                  ips?.map((item, key) => (
                    <City
                      key={key}
                      city={item?.city || translate('cms.dashboard.unknown')}
                      num={item?.num}
                    />
                  ))) || <Empty />}
              </Skeleton>
            </div>
          </Card>
        </div>
        <div className='flex-grow'>
          <Card title={translate('cms.dashboard.article.name')} headerBordered className='mb-4'>
            <div className='flex flex-col -my-3 divide-y divide-gray-3 dark:divide-gray-9'>
              <Skeleton theme={'paragraph'} loading={!data?.data} className='my-3'>
                {(data?.data &&
                  data?.data?.length > 0 &&
                  data?.data?.map((item, key) => {
                    return (
                      <ListItem
                        key={key}
                        title={item?.title}
                        id={item?.id}
                        time={item?.created_at}
                        view={item?.view}
                      />
                    )
                  })) || <Empty />}
              </Skeleton>
            </div>
          </Card>

          <div className='grid-colos-1 grid gap-4 lg:grid-cols-2'>
            <Card title={translate('cms.dashboard.push.name')}>
              <div className='h-56 w-full'>
                <ChartBar
                  labels={push?.labels}
                  data={[{ name: translate('cms.dashboard.push.num'), data: push?.nums }]}
                />
              </div>
            </Card>
            <Card title={translate('cms.dashboard.class.name')}>
              <div className='h-56 w-full'>
                <ChartBar
                  labels={classify?.labels}
                  data={[{ name: translate('cms.dashboard.class.num'), data: classify?.nums }]}
                />
              </div>
            </Card>
          </div>
        </div>
      </div>
    </Main>
  )
}

const Empty = () => {
  const translate = useTranslate()

  return (
    <div className='h-40 flex items-center justify-center text-secondary'>
      {translate('cms.dashboard.noData')}
    </div>
  )
}

interface CityProps {
  city: string
  num?: number
}

const City = ({ city, num }: CityProps) => {
  return (
    <div className='flex justify-between'>
      <div className='flex items-center gap-2'>{city}</div>
      <div>{num || 0}</div>
    </div>
  )
}

interface BrowserProps {
  name: string
  num?: number
}

import IEIcon from '../../../../static/browsers/ie.svg'
import EdgeIcon from '../../../../static/browsers/edge.svg'
import ChromeIcon from '../../../../static/browsers/chrome.svg'
import FirefoxIcon from '../../../../static/browsers/firefox.svg'
import OperaIcon from '../../../../static/browsers/opera.svg'
import SafariIcon from '../../../../static/browsers/safari.svg'
import OtherIcon from '../../../../static/browsers/other.svg'

const Browser = ({ name, num }: BrowserProps) => {
  return (
    <div className='flex justify-between'>
      <div className='flex items-center gap-2'>
        <div className={clsx(['block h-4 w-4'])}>
          {name === 'Ie' && <img src={IEIcon} />}
          {name === 'Chrome' && <img src={ChromeIcon} />}
          {name === 'Edge' && <img src={EdgeIcon} />}
          {name === 'Firefox' && <img src={FirefoxIcon} />}
          {name === 'Opera' && <img src={OperaIcon} />}
          {name === 'Safari' && <img src={SafariIcon} />}
          {name != 'Ie' &&
            name != 'Chrome' &&
            name != 'Edge' &&
            name != 'Firefox' &&
            name != 'Opera' &&
            name != 'Safari' && <img src={OtherIcon} />}
        </div>
        {name}
      </div>
      <div>{num || 0}</div>
    </div>
  )
}

interface ListItemProps {
  id?: number
  title?: string
  time?: string
  view?: number
}
const ListItem = ({ id, title, time, view }: ListItemProps) => {
  const translate = useTranslate()
  const go = useGo()
  return (
    <div className='flex flex-col gap-1 py-4'>
      <div>
        <Link
          className='font-bold'
          onClick={() => {
            go({
              to: `/admin/content/article/page/${id}`,
            })
          }}
        >
          {title}
        </Link>
      </div>
      <div className='flex gap-2 text-xs text-secondary'>
        <div>
          {translate('cms.dashboard.article.pv')} {view}
        </div>
        <div>
          {translate('cms.dashboard.article.date')} {time}
        </div>
      </div>
    </div>
  )
}

interface CardProps {
  color: string
  icon?: string
  title?: string
  num?: any
}

const CardItem = ({ title, num, icon, color = 'brand' }: CardProps) => {
  const colorMaps: Record<string, Array<string>> = {
    warning: ['bg-warning-1', 'text-warning'],
    brand: ['bg-brand-1', 'text-brand'],
    success: ['bg-success-1', 'text-success'],
    error: ['bg-error-1', 'text-error'],
  }

  return (
    <Card className='min-w-40'>
      <div className='mt-4 flex flex-col items-center'>
        <div
          className={clsx(['flex items-center justify-center rounded p-2', ...colorMaps[color]])}
        >
          <div className={clsx(['text-2xl', icon])}></div>
        </div>
      </div>
      <div className='mt-4 flex flex-col items-center'>
        <div className='text-xs text-gray-400'>{title || <Skeleton> </Skeleton>}</div>
        <div className='mt-1 text-xl'>{num || 0}</div>
      </div>
    </Card>
  )
}

export default Index
