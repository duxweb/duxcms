import React from 'react'
import { useTranslate } from '@refinedev/core'
import { PrimaryTableCol, Link, Tag, Button } from 'tdesign-react/esm'
import { PageTable, MediaText, Modal } from '@duxweb/dux-refine'
import { Icon } from 'tdesign-icons-react'
import dayjs from 'dayjs'

const List = () => {
  const translate = useTranslate()

  const columns = React.useMemo<PrimaryTableCol[]>(
    () => [
      {
        colKey: 'name',
        title: translate('cloud.apps.fields.name'),
        minWidth: 300,
        cell: ({ row }) => {
          return (
            <MediaText size='small'>
              <MediaText.Image src={row.icon}></MediaText.Image>
              <MediaText.Title>{row.title}</MediaText.Title>
              <MediaText.Desc>{row.desc}</MediaText.Desc>
            </MediaText>
          )
        },
      },
      {
        colKey: 'time',
        title: translate('cloud.apps.fields.time'),
        cell: ({ row }) => {
          return <>{dayjs(row.local_time * 1000).format('YYYY-MM-DD HH:mm:ss')}</>
        },
      },
      {
        colKey: 'update',
        title: translate('cloud.apps.fields.update'),
        cell: ({ row }) => {
          return (
            <>
              {!row?.update ? (
                <Tag variant='outline'>暂无更新</Tag>
              ) : (
                <Modal
                  title={translate('cloud.apps.action.update')}
                  trigger={
                    <Tag theme='warning' variant='outline' className='cursor-pointer'>
                      有更新
                    </Tag>
                  }
                  component={() => import('./update')}
                  componentProps={{ data: row }}
                ></Modal>
              )}
            </>
          )
        },
      },
      {
        colKey: 'link',
        title: translate('table.actions'),
        fixed: 'right',
        align: 'center',
        width: 160,
        cell: ({ row }) => {
          return (
            <div className='flex justify-center gap-4'>
              <Link
                theme='primary'
                href={`https://www.dux.cn/page/apps/info/` + row.id}
                target='_black'
              >
                {translate('buttons.show')}
              </Link>

              <Modal
                title={translate('cloud.apps.action.uninstall')}
                trigger={<Link theme='danger'>{translate('cloud.apps.action.uninstall')}</Link>}
                component={() => import('./uninstall')}
                componentProps={{ name: row.name }}
              ></Modal>
            </div>
          )
        },
      },
    ],
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [translate],
  )
  return (
    <>
      <PageTable
        columns={columns}
        table={{
          rowKey: 'id',
          pagination: {
            disabled: true,
          },
        }}
        title={translate('cloud.apps.name')}
        actionRender={() => (
          <>
            <Modal
              title={translate('cloud.apps.action.login')}
              trigger={
                <Button icon={<Icon name='user' />} variant='outline' theme='primary'>
                  {translate('cloud.apps.action.login')}
                </Button>
              }
              component={() => import('./login')}
            ></Modal>
            <Modal
              title={translate('cloud.apps.action.install')}
              trigger={
                <Button icon={<Icon name='uninstall' />} variant='outline' theme='success'>
                  {translate('cloud.apps.action.install')}
                </Button>
              }
              component={() => import('./install')}
            ></Modal>
          </>
        )}
      />
    </>
  )
}

export default List
